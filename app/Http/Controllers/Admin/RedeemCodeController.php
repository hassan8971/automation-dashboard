<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RedeemCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RedeemCodeController extends Controller
{
    public function index()
    {
        $codes = RedeemCode::latest()->paginate(20);
        return view('admin.redeem-codes.index', compact('codes'));
    }

    public function create()
    {
        return view('admin.redeem-codes.create');
    }

    public function store(Request $request)
    {
        // 1. حذف کاما از مبلغ
        if ($request->has('amount')) {
            $request->merge(['amount' => str_replace(',', '', $request->input('amount'))]);
        }

        $validated = $request->validate([
            'creation_type' => 'required|in:single,bulk', // تکی یا انبوه
            'amount' => 'required|numeric|min:0',
            'service_type' => 'required|string|in:' . implode(',', array_keys(RedeemCode::SERVICES)), // ✅ Validate against Model keys
            'usage_limit' => 'required|integer|min:1',
            'expires_at' => 'nullable|date',
            'is_active' => 'nullable|boolean',
            
            // اگر تکی بود، کد اجباری است. اگر انبوه بود، تعداد اجباری است.
            'code' => 'required_if:creation_type,single|nullable|string|unique:redeem_codes,code',
            'quantity' => 'required_if:creation_type,bulk|nullable|integer|min:1|max:1000',
            'prefix' => 'nullable|string|max:10', // پیشوند برای تولید انبوه
        ]);

        $isActive = $request->has('is_active');
        $data = [
            'amount' => $validated['amount'],
            'service_type' => $validated['service_type'],
            'usage_limit' => $validated['usage_limit'],
            'expires_at' => $validated['expires_at'],
            'is_active' => $isActive,
        ];

        // حالت اول: ایجاد تکی
        if ($request->creation_type === 'single') {
            RedeemCode::create(array_merge($data, ['code' => $validated['code']]));
            $msg = 'کد با موفقیت ایجاد شد.';
        } 
        // حالت دوم: تولید انبوه (Random)
        else {
            $count = $validated['quantity'];
            $prefix = $request->input('prefix', '');
            
            for ($i = 0; $i < $count; $i++) {
                // تلاش برای ساخت کد یکتا
                $randomString = strtoupper(Str::random(8));
                $finalCode = $prefix ? "{$prefix}-{$randomString}" : $randomString;
                
                // چک کردن تکراری نبودن (ساده)
                while(RedeemCode::where('code', $finalCode)->exists()) {
                    $randomString = strtoupper(Str::random(8));
                    $finalCode = $prefix ? "{$prefix}-{$randomString}" : $randomString;
                }

                RedeemCode::create(array_merge($data, ['code' => $finalCode]));
            }
            $msg = "$count کد تصادفی با موفقیت تولید شد.";
        }

        return redirect()->route('admin.redeem-codes.index')->with('success', $msg);
    }

    public function edit(RedeemCode $redeemCode)
    {
        return view('admin.redeem-codes.edit', compact('redeemCode'));
    }

    public function update(Request $request, RedeemCode $redeemCode)
    {
        // حذف کاما
        if ($request->has('amount')) {
            $request->merge(['amount' => str_replace(',', '', $request->input('amount'))]);
        }

        $validated = $request->validate([
            'code' => 'required|string|unique:redeem_codes,code,' . $redeemCode->id,
            'amount' => 'required|numeric|min:0',
            'service_type' => 'required|string|in:' . implode(',', array_keys(RedeemCode::SERVICES)), // ✅ Validate against Model keys
            'usage_limit' => 'required|integer|min:1',
            'expires_at' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        $redeemCode->update([
            'code' => $validated['code'],
            'amount' => $validated['amount'],
            'service_type' => $validated['service_type'],
            'usage_limit' => $validated['usage_limit'],
            'expires_at' => $validated['expires_at'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.redeem-codes.index')->with('success', 'کد ویرایش شد.');
    }

    public function destroy(RedeemCode $redeemCode)
    {
        $redeemCode->delete();
        return redirect()->route('admin.redeem-codes.index')->with('success', 'کد حذف شد.');
    }
}