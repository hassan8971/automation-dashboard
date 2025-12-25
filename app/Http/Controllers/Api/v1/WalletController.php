<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WalletTransaction;
use App\Http\Resources\api\v1\WalletTransactionResource;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    /**
     * 1. Get Wallet Balance
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Ensure wallet exists
        if (!$user->wallet) {
            $user->wallet()->create(['balance' => 0]);
            $user->load('wallet');
        }

        return response()->json([
            'balance' => (int) $user->wallet->balance,
            'formatted_balance' => number_format($user->wallet->balance) . ' تومان',
            'currency' => 'تومان',
            'is_active' => (boolean) $user->wallet->is_active,
        ]);
    }

    /**
     * 2. Get Transaction History
     */
    public function history(Request $request)
    {
        $user = $request->user();
        
        if (!$user->wallet) {
            return WalletTransactionResource::collection([]);
        }

        $transactions = $user->wallet->transactions()
            ->latest()
            ->paginate(20);

        return WalletTransactionResource::collection($transactions);
    }

    /**
     * 3. Deposit Money (Mock Payment)
     * In a real app, this would redirect to a bank gateway.
     * Here, it instantly charges the wallet.
     */
    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000', // Minimum 1000 Tomans
        ]);

        $user = $request->user();
        $amount = $request->amount;

        if (!$user->wallet) {
            $user->wallet()->create(['balance' => 0]);
        }

        // Simulate a successful bank transaction
        try {
            DB::beginTransaction();

            // Use the helper method we created in the Wallet Model
            $user->wallet->deposit(
                $amount,
                WalletTransaction::TYPE_DEPOSIT,
                'شارژ آنلاین کیف پول (تست)',
                'confirmed', // Status
                'REF-' . strtoupper(uniqid()) // Fake Bank Reference ID
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'کیف پول با موفقیت شارژ شد.',
                'new_balance' => number_format($user->wallet->balance) . ' تومان'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در عملیات شارژ'], 500);
        }
    }
}