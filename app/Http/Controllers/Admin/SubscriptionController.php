<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Gift; // برای لیست گیفت‌ها
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::with('gift')->latest()->get();
        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    public function create()
    {
        $gifts = Gift::where('is_active', true)->get();
        return view('admin.subscriptions.create', compact('gifts'));
    }

    public function store(Request $request)
    {
        $this->saveSubscription($request, new Subscription());
        return redirect()->route('admin.subscriptions.index')->with('success', 'اشتراک ایجاد شد.');
    }

    public function edit(Subscription $subscription)
    {
        $gifts = Gift::where('is_active', true)->get();
        return view('admin.subscriptions.edit', compact('subscription', 'gifts'));
    }

    public function update(Request $request, Subscription $subscription)
    {
        $this->saveSubscription($request, $subscription);
        return redirect()->route('admin.subscriptions.index')->with('success', 'اشتراک ویرایش شد.');
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();
        return redirect()->route('admin.subscriptions.index')->with('success', 'اشتراک حذف شد.');
    }

    private function saveSubscription(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_in_days' => 'required|integer|min:1',
            'description' => 'nullable|string', // اضافه شد
            'gift_id' => 'nullable|exists:gifts,id', // اضافه شد
            // 'supported_apps' حذف شد
            'is_active' => 'nullable|boolean',
        ]);

        $subscription->name = $validated['name'];
        if (!$subscription->exists || $subscription->isDirty('name')) {
             $subscription->slug = Str::slug($validated['name']);
        }
        $subscription->price = $validated['price'];
        $subscription->duration_in_days = $validated['duration_in_days'];
        $subscription->description = $validated['description'];
        $subscription->gift_id = $validated['gift_id'];
        $subscription->is_active = $request->has('is_active');
        
        $subscription->save();
    }
}