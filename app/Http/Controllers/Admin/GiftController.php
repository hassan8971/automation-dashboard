<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gift;
use App\Models\Subscription;
use App\Models\Addon;
use Illuminate\Http\Request;

class GiftController extends Controller
{
    public function index()
    {
        // triggerable را حذف کردیم
        $gifts = Gift::with(['rewardable'])->latest()->get();
        return view('admin.gifts.index', compact('gifts'));
    }

    public function create()
    {
        // برای انتخاب جایزه هنوز به لیست اشتراک‌ها و ادآن‌ها نیاز داریم
        $subscriptions = Subscription::where('is_active', true)->get();
        $addons = Addon::where('is_active', true)->get();
        return view('admin.gifts.create', compact('subscriptions', 'addons'));
    }

    public function store(Request $request)
    {
        $this->saveGift($request, new Gift());
        return redirect()->route('admin.gifts.index')->with('success', 'هدیه ایجاد شد.');
    }

    public function edit(Gift $gift)
    {
        $subscriptions = Subscription::where('is_active', true)->get();
        $addons = Addon::where('is_active', true)->get();
        return view('admin.gifts.edit', compact('gift', 'subscriptions', 'addons'));
    }

    public function update(Request $request, Gift $gift)
    {
        $this->saveGift($request, $gift);
        return redirect()->route('admin.gifts.index')->with('success', 'هدیه ویرایش شد.');
    }

    public function destroy(Gift $gift)
    {
        $gift->delete();
        return redirect()->route('admin.gifts.index')->with('success', 'هدیه حذف شد.');
    }

    private function saveGift(Request $request, Gift $gift)
    {
        // 1. Clean Amount Comma
        if ($request->has('generated_amount')) {
            $request->merge(['generated_amount' => str_replace(',', '', $request->input('generated_amount'))]);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:subscription,addon,redeem_code,app,dynamic_app,custom',
            
            'reward_subscription_id' => 'required_if:type,subscription|nullable|exists:subscriptions,id',
            'reward_addon_id' => 'required_if:type,addon|nullable|exists:addons,id',
            
            // برای اپ و متن سفارشی
            'payload' => 'nullable|string', 
            
            // فیلدهای تنظیمات Redeem Code
            'generated_amount' => 'required_if:type,redeem_code|nullable|numeric|min:0',
            'generated_service_type' => 'required_if:type,redeem_code|nullable|string',
            'generated_access_level' => 'required_if:type,redeem_code|nullable|in:exclusive,shareable',
            
            'is_active' => 'nullable|boolean',
        ]);

        $gift->title = $validated['title'];
        $gift->type = $validated['type'];
        $gift->is_active = $request->has('is_active');
        
        // Reset defaults
        $gift->rewardable_type = null;
        $gift->rewardable_id = null;
        $gift->payload = null;
        $gift->generated_amount = null;
        $gift->generated_service_type = null;
        $gift->generated_access_level = null;

        if ($validated['type'] === 'subscription') {
            $gift->rewardable_type = SubscriptionPlan::class;
            $gift->rewardable_id = $validated['reward_subscription_id'];
        } 
        elseif ($validated['type'] === 'addon') {
            $gift->rewardable_type = Addon::class;
            $gift->rewardable_id = $validated['reward_addon_id'];
        }
        // Redeem Code Logic
        elseif ($validated['type'] === 'redeem_code') {
            $gift->generated_amount = $validated['generated_amount'];
            $gift->generated_service_type = $validated['generated_service_type'];
            $gift->generated_access_level = $validated['generated_access_level'];
        }
        // App / Custom Logic
        elseif (in_array($validated['type'], ['app', 'custom'])) {
            $gift->payload = $validated['payload'];
        }
        // Dynamic App Logic
        elseif ($validated['type'] === 'dynamic_app') {
            // Nothing to save specifically, logical marker only
        }

        $gift->save();
    }
}