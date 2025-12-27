<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RedeemCode; // مدلی که قبلا داشتید
use App\Models\WalletTransaction;

class RedeemCodeController extends Controller
{
    public function redeem(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'service_type' => 'nullable|string',
        ]);

        $inputCode = trim($request->code);

        $requestedService = $request->input('service_type', 'appstore');

        $user = $request->user();

        // 1. پیدا کردن کد
        $redeemCode = RedeemCode::where('code', $inputCode)->first();

        // 2. بررسی‌های اولیه
        if (!$redeemCode) {
            return response()->json(['message' => 'کد وارد شده نامعتبر است.'], 404);
        }

        if (!$redeemCode->is_active) {
            return response()->json(['message' => 'این کد غیرفعال شده است.'], 400);
        }

        if ($redeemCode->service_type !== 'all' && $redeemCode->service_type !== $requestedService) {
            return response()->json([
                'message' => 'این کد برای سرویس فعلی شما معتبر نیست.'
            ], 400);
        }

        // 3. بررسی تاریخ انقضا
        if ($redeemCode->expires_at && $redeemCode->expires_at->isPast()) {
            return response()->json(['message' => 'تاریخ انقضای این کد گذشته است.'], 400);
        }

        // 4. بررسی محدودیت تعداد استفاده (Usage Limit)
        if ($redeemCode->used_count >= $redeemCode->usage_limit) {
            return response()->json(['message' => 'ظرفیت استفاده از این کد تکمیل شده است.'], 400);
        }

        // نکته امنیتی: اگر کد عمومی است (limit > 1)، اینجا باید چک کنید که کاربر قبلاً آن را نزده باشد.
        // اما چون در ساختار دیتابیس فعلی شما جدول واسط (Pivot) برای ثبت تاریخچه استفاده یوزرها نداریم،
        // فرض را بر این می‌گذاریم که کدها یکبار مصرف (Usage Limit = 1) هستند یا ریسک استفاده تکراری پذیرفته شده است.

        try {
            DB::beginTransaction();

            // الف) شارژ کیف پول کاربر
            if (!$user->wallet) {
                $user->wallet()->create(['balance' => 0]);
                $user->load('wallet'); // رفرش کردن رابطه
            }

            $user->wallet->deposit(
                $redeemCode->amount,
                WalletTransaction::TYPE_DEPOSIT, 
                "شارژ با کد هدیه: {$inputCode}"
            );

            // ب) آپدیت شمارنده استفاده
            $redeemCode->increment('used_count');

            // اگر ظرفیت پر شد، کد را غیرفعال کن (اختیاری)
            if ($redeemCode->used_count >= $redeemCode->usage_limit) {
                $redeemCode->update(['is_active' => false]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => number_format($redeemCode->amount) . ' تومان به کیف پول شما اضافه شد.',
                'new_balance' => number_format($user->wallet->fresh()->balance)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در عملیات: ' . $e->getMessage()], 500);
        }
    }
}