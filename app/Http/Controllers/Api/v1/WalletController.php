<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WalletTransaction;
use App\Models\Wallet; // اضافه شد
use App\Http\Resources\api\v1\WalletTransactionResource; // مطمئن شوید مسیر درست است
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    /**
     * 1. Get Wallet Balance (Specific Service)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // دریافت نام سرویس از ورودی (پیش‌فرض appstore)
        $serviceName = $request->input('service_type', 'appstore');
        
        // پیدا کردن یا ساختن کیف پول مخصوص آن سرویس
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id, 'service_name' => $serviceName],
            ['balance' => 0, 'is_active' => true]
        );

        return response()->json([
            'service' => $serviceName,
            'balance' => (int) $wallet->balance,
            'formatted_balance' => number_format($wallet->balance) . ' تومان',
            'currency' => 'تومان',
            'is_active' => (boolean) $wallet->is_active,
        ]);
    }

    /**
     * 2. Get Transaction History (Filtered by Service)
     */
    public function history(Request $request)
    {
        $user = $request->user();
        $serviceName = $request->input('service_type', 'appstore');

        $wallet = Wallet::where('user_id', $user->id)
                        ->where('service_name', $serviceName)
                        ->first();
        
        if (!$wallet) {
            return response()->json(['data' => []]);
        }

        $transactions = $wallet->transactions()
            ->latest()
            ->paginate(20);

        // اگر ریسورس شما نیاز به تغییر دارد، باید آن را هم آپدیت کنید که سرویس را نشان دهد
        return WalletTransactionResource::collection($transactions);
    }

    /**
     * 3. Deposit Money (Mock Payment)
     */
    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'service_type' => 'nullable|string', // ورودی سرویس
        ]);

        $user = $request->user();
        $amount = $request->amount;
        $serviceName = $request->input('service_type', 'appstore');

        try {
            DB::beginTransaction();

            // پیدا کردن یا ساختن کیف پول سرویس مورد نظر
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $user->id, 'service_name' => $serviceName],
                ['balance' => 0]
            );

            // متد deposit که آپدیت کردیم، خودش service_name را در تراکنش ثبت می‌کند
            $wallet->deposit(
                $amount,
                WalletTransaction::TYPE_DEPOSIT,
                "شارژ آنلاین ($serviceName)",
                'confirmed',
                'REF-' . strtoupper(uniqid())
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'کیف پول با موفقیت شارژ شد.',
                'service' => $serviceName,
                'new_balance' => number_format($wallet->balance) . ' تومان'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در عملیات شارژ'], 500);
        }
    }
}