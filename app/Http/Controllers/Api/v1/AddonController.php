<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Addon;
use App\Models\UserAddon;
use App\Models\WalletTransaction;
use App\Http\Resources\api\v1\AddonResource; // Created in previous steps

class AddonController extends Controller
{
    /**
     * 1. List All Available Add-ons
     */
    public function index()
    {
        $addons = Addon::where('is_active', true)->get();
        return AddonResource::collection($addons);
    }

    /**
     * 2. List My Purchased Add-ons
     */
    public function myAddons(Request $request)
    {
        $user = $request->user();

        $myAddons = UserAddon::with('addon')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->latest()
            ->get();

        return response()->json([
            'data' => $myAddons->map(function ($ua) {
                return [
                    'id' => $ua->id,
                    'addon_title' => $ua->addon->title ?? $ua->addon->name,
                    'purchased_at' => $ua->created_at->toIso8601String(),
                    'price_paid' => number_format($ua->price_paid) . ' تومان',
                ];
            })
        ]);
    }

    /**
     * 3. Purchase an Add-on
     */
    public function purchase(Request $request)
    {
        $request->validate([
            'addon_id' => 'required|exists:addons,id',
        ]);

        $user = $request->user();
        $user->load('wallet'); // Refresh wallet balance

        $addon = Addon::findOrFail($request->addon_id);

        // Check if user already owns this (Prevent duplicates if it's a "One-Time Feature")
        // Remove this check if users can buy consumables multiple times (like "500 Coins")
        $alreadyOwns = UserAddon::where('user_id', $user->id)
            ->where('addon_id', $addon->id)
            ->where('status', 'active')
            ->exists();

        if ($alreadyOwns && $addon->type === 'permanent') { 
             // Assuming you have a 'type' column to distinguish consumable vs permanent
             return response()->json(['message' => 'شما قبلاً این آیتم را خریداری کرده‌اید.'], 400);
        }

        // Check Wallet Balance
        if (!$user->wallet || $user->wallet->balance < $addon->price) {
            return response()->json([
                'message' => 'موجودی کیف پول کافی نیست.',
                'required' => $addon->price,
                'balance' => $user->wallet->balance ?? 0
            ], 402);
        }

        try {
            DB::beginTransaction();

            // A. Deduct Money
            $user->wallet->withdraw(
                $addon->price,
                WalletTransaction::TYPE_WITHDRAW,
                "خرید افزودنی: {$addon->name}"
            );

            // B. Create Record
            $userAddon = UserAddon::create([
                'user_id' => $user->id,
                'addon_id' => $addon->id,
                'price_paid' => $addon->price,
                'status' => 'active',
                // If the addon has a duration (like "7 Days Boost"), calculate expiry
                // 'expires_at' => $addon->duration_days ? now()->addDays($addon->duration_days) : null
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'افزودنی با موفقیت خریداری شد.',
                'data' => [
                    'addon' => $addon->name,
                    'purchased_at' => $userAddon->created_at->toIso8601String(),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'خطا در تراکنش: ' . $e->getMessage()
            ], 500);
        }
    }
}