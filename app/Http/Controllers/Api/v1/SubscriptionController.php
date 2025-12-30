<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Subscription;
use App\Models\UserSubscription;
use App\Models\WalletTransaction;
use App\Http\Resources\Api\v1\SubscriptionResource;

class SubscriptionController extends Controller
{
    /**
     * 1. List all available plans
     */
    public function index()
    {
        $plans = Subscription::where('is_active', true)
            ->orderBy('price', 'asc')
            ->get();
            
        return SubscriptionResource::collection($plans);
    }

    /**
     * 2. Get Current User's Active Subscription
     */
    public function current(Request $request)
    {
        $user = $request->user();

        // Get the latest active subscription
        $currentSub = UserSubscription::with('plan')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->latest('starts_at')
            ->first();

        if (!$currentSub) {
            return response()->json(['data' => null, 'message' => 'شما اشتراک فعالی ندارید.']);
        }

        return response()->json([
            'data' => [
                'plan_name' => $currentSub->plan->name,
                'starts_at' => $currentSub->starts_at->toIso8601String(),
                'expires_at' => $currentSub->expires_at->toIso8601String(),
                'days_remaining' => (int) now()->diffInDays($currentSub->expires_at, false),
                'is_valid' => true
            ]
        ]);
    }

    /**
     * 3. BUY & ACTIVATE Subscription
     */
    public function purchase(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
        ]);

        $user = $request->user();
        $newPlan = Subscription::findOrFail($request->subscription_id);

        // --- STEP 1: Analyze Current Situation ---
        // We eager load 'plan' (or 'subscription') to check the OLD price
        $currentSub = UserSubscription::with('plan') 
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->latest('starts_at')
            ->first();

        $payableAmount = $newPlan->price;
        $discountAmount = 0;
        $isUpgrade = false;

        if ($currentSub) {
            // A. Prevent buying the exact same plan
            if ($currentSub->subscription_id == $newPlan->id) {
                return response()->json([
                    'message' => 'شما هم‌اکنون همین اشتراک را فعال دارید.'
                ], 400);
            }

            // B. 🚫 PREVENT DOWNGRADE (New Logic) 🚫
            // If new plan is cheaper than current plan, block it.
            // Using price as the hierarchy factor.
            $currentPlanPrice = $currentSub->plan->price ?? 0;
            
            if ($newPlan->price < $currentPlanPrice) {
                return response()->json([
                    'message' => 'امکان تغییر به اشتراک پایین‌تر (Downgrade) وجود ندارد. فقط ارتقاء مجاز است.',
                    'action' => 'downgrade_not_allowed'
                ], 400);
            }

            $isUpgrade = true;

            // --- PRORATION MATH ---
            
            // Calculate Days Remaining
            $daysRemaining = max(0, now()->diffInDays($currentSub->expires_at, false));
            
            // Calculate Total Duration of the OLD plan
            $startDate = $currentSub->starts_at ?? $currentSub->created_at;
            $totalDuration = $startDate->diffInDays($currentSub->expires_at);
            $totalDuration = $totalDuration > 0 ? $totalDuration : 30;

            // Daily Rate = Amount Paid / Total Days
            $oldPricePaid = $currentSub->price_paid; 
            $dailyRate = $oldPricePaid / $totalDuration;

            // Discount = Daily Rate * Remaining Days
            $discountAmount = round($dailyRate * $daysRemaining);

            // Final Price = New Price - Discount
            $payableAmount = max(0, $newPlan->price - $discountAmount);
        }

        // --- STEP 2: Wallet Balance Check ---
        if (!$user->wallet || $user->wallet->balance < $payableAmount) {
            return response()->json([
                'message' => 'موجودی کیف پول کافی نیست. لطفا ابتدا کیف پول خود را شارژ کنید.',
                'required_amount' => $payableAmount,
                'discount_applied' => $discountAmount,
                'current_balance' => $user->wallet->balance ?? 0
            ], 402); 
        }

        // --- STEP 3: Transaction Execution ---
        try {
            DB::beginTransaction();

            // A. Deduct Money (if price > 0)
            if ($payableAmount > 0) {
                $description = $isUpgrade 
                    ? "ارتقاء اشتراک به {$newPlan->name} (با کسر {$discountAmount} تومان اعتبار قبلی)" 
                    : "خرید اشتراک: {$newPlan->name}";

                $user->wallet->withdraw(
                    $payableAmount,
                    WalletTransaction::TYPE_WITHDRAW, 
                    $description
                );
            }

            // B. Expire the Old Subscription
            if ($currentSub) {
                $currentSub->update([
                    'status' => 'upgraded',
                    'expires_at' => now(), 
                ]);
            }

            // C. Create New Subscription
            $userSub = UserSubscription::create([
                'user_id' => $user->id,
                'subscription_id' => $newPlan->id,
                'starts_at' => now(),
                'expires_at' => now()->addDays($newPlan->duration_in_days),
                'price_paid' => $payableAmount, 
                'status' => 'active'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $isUpgrade ? 'اشتراک شما با موفقیت ارتقاء یافت.' : 'اشتراک با موفقیت فعال شد.',
                'data' => [
                    'plan' => $newPlan->name,
                    'price_paid' => $payableAmount,
                    'discount_used' => $discountAmount,
                    'expires_at' => $userSub->expires_at->toIso8601String(),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'خطا در انجام تراکنش: ' . $e->getMessage()
            ], 500);
        }
    }
}