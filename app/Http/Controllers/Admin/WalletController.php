<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $query = Wallet::with('user');

        // منطق جستجو
        if ($request->filled('search')) {
            $search = $request->input('search');
            
            // جستجو در جدول یوزرها (نام، ایمیل، شماره موبایل)
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  // نکته: اگر نام ستون موبایل شما در دیتابیس mobile است، اینجا mobile بنویسید
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        if ($request->filled('service')) {
            $query->where('service_name', $request->service);
        }

        // استفاده از withQueryString برای اینکه در صفحه ۲ و ۳ جستجو نپرد
        $wallets = $query->latest('updated_at')
                         ->paginate(20)
                         ->withQueryString();

        return view('admin.wallets.index', compact('wallets'));
    }

    public function show(Wallet $wallet)
    {
        // مشاهده تاریخچه تراکنش‌های یک کاربر خاص
        $transactions = $wallet->transactions()->paginate(20);
        return view('admin.wallets.show', compact('wallet', 'transactions'));
    }

    // متد برای شارژ یا کسر دستی توسط ادمین
    public function updateBalance(Request $request, Wallet $wallet)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'type' => 'required|in:add,sub', // add = افزایش، sub = کاهش
            'description' => 'nullable|string|max:255',
        ]);

        $amount = str_replace(',', '', $request->amount); // حذف کاما اگر فرانت می‌فرستد

        try {
            DB::beginTransaction();

            if ($request->type === 'add') {
                $wallet->deposit(
                    $amount, 
                    WalletTransaction::TYPE_MANUAL_ADD, 
                    $request->description ?? 'شارژ دستی توسط مدیریت'
                );
                $msg = 'مبلغ به کیف پول اضافه شد.';
            } else {
                $wallet->withdraw(
                    $amount, 
                    WalletTransaction::TYPE_MANUAL_SUB, 
                    $request->description ?? 'کسر دستی توسط مدیریت'
                );
                $msg = 'مبلغ از کیف پول کسر شد.';
            }

            DB::commit();
            return back()->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'خطا: ' . $e->getMessage());
        }
    }
}