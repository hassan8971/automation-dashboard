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
        $plan = Subscription::findOrFail($request->subscription_id);

        // Check 1: Does user already have an active subscription?
        $existingSub = UserSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();

        if ($existingSub) {
            return response()->json([
                'message' => 'شما در حال حاضر یک اشتراک فعال دارید. لطفا تا پایان آن صبر کنید.'
            ], 400);
        }

        // Check 2: Wallet Balance
        // We assume payment is done via Wallet for instant activation
        if (!$user->wallet || $user->wallet->balance < $plan->price) {
            return response()->json([
                'message' => 'موجودی کیف پول کافی نیست. لطفا ابتدا کیف پول خود را شارژ کنید.',
                'required' => $plan->price,
                'balance' => $user->wallet->balance ?? 0
            ], 402); // 402 Payment Required
        }

        // Transaction Logic
        try {
            DB::beginTransaction();

            // A. Deduct Money
            $user->wallet->withdraw(
                $plan->price,
                WalletTransaction::TYPE_WITHDRAW,
                "خرید اشتراک: {$plan->name}"
            );

            // B. Create User Subscription
            $userSub = UserSubscription::create([
                'user_id' => $user->id,
                'subscription_id' => $plan->id,
                'starts_at' => now(),
                'expires_at' => now()->addDays($plan->duration_in_days),
                'price_paid' => $plan->price,
                'status' => 'active'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'اشتراک با موفقیت خریداری و فعال شد.',
                'data' => [
                    'plan' => $plan->name,
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