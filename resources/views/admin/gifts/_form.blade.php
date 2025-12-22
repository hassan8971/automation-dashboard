@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-right">
        <strong class="font-bold">خطا!</strong>
        <ul class="mt-2 list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div x-data="{ 
        giftType: '{{ old('type', $gift->type ?? 'subscription') }}',
    }" class="space-y-6">

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">عنوان هدیه</label>
        <input type="text" name="title" value="{{ old('title', $gift->title ?? '') }}" required 
               class="w-full px-4 py-2 border rounded-lg dark:bg-dark-bg dark:border-gray-600 dark:text-white" 
               placeholder="مثال: کد تخفیف 50 تومانی">
    </div>

    <hr class="border-gray-200 dark:border-gray-700">

    <div>
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">تعریف جایزه</h3>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">نوع هدیه</label>
            <select x-model="giftType" name="type" class="w-full px-4 py-2 border rounded-lg dark:bg-dark-bg dark:border-gray-600 dark:text-white">
                <option value="subscription">اشتراک (Subscription)</option>
                <option value="addon">افزونه (Add-on)</option>
                <option value="redeem_code">کد هدیه / شارژ (Redeem Code)</option>
                <option value="app">اپلیکیشن ثابت (App)</option>
                <option value="dynamic_app">اپلیکیشن هوشمند (Dynamic App)</option>
                <option value="custom">متن سفارشی / دیگر</option>
            </select>
        </div>

        <div x-show="giftType === 'subscription'" class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
            <label class="block text-sm font-medium text-blue-800 dark:text-blue-300 mb-2">کدام اشتراک هدیه داده شود؟</label>
            <select name="reward_subscription_id" class="w-full px-3 py-2 border rounded-lg dark:bg-dark-bg dark:border-gray-600 dark:text-white">
                <option value="">انتخاب کنید...</option>
                @foreach($subscriptions as $sub)
                    <option value="{{ $sub->id }}" @if(isset($gift) && $gift->rewardable_id == $sub->id) selected @endif>
                        {{ $sub->name }} ({{ $sub->duration_in_days }} روزه)
                    </option>
                @endforeach
            </select>
        </div>

        <div x-show="giftType === 'addon'" class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
            <label class="block text-sm font-medium text-purple-800 dark:text-purple-300 mb-2">کدام افزونه هدیه داده شود؟</label>
            <select name="reward_addon_id" class="w-full px-3 py-2 border rounded-lg dark:bg-dark-bg dark:border-gray-600 dark:text-white">
                <option value="">انتخاب کنید...</option>
                @foreach($addons as $addon)
                    <option value="{{ $addon->id }}" @if(isset($gift) && $gift->rewardable_id == $addon->id) selected @endif>
                        {{ $addon->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div x-show="giftType === 'redeem_code'" class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
            <div class="flex items-center gap-2 mb-3 text-green-800 dark:text-green-300">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h4 class="font-bold text-sm">تنظیمات تولید خودکار کد</h4>
            </div>
            
            <p class="text-xs text-green-700 dark:text-green-400 mb-4">
                هنگامی که خرید کاربر نهایی شود، سیستم یک کد منحصر‌به‌فرد با مشخصات زیر برای او تولید خواهد کرد.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Amount --}}
                <div x-data="{ 
                    rawPrice: '{{ old('generated_amount', $gift->generated_amount ?? '') }}',
                    format(value) { return value ? value.toString().replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',') : ''; },
                    update(event) {
                        let val = event.target.value.replace(/[^0-9]/g, '');
                        this.rawPrice = val;
                        event.target.value = this.format(val);
                    }
                }">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">مبلغ کد (تومان)</label>
                    <input type="text" inputmode="numeric" 
                           class="w-full px-3 py-2 border rounded-lg dark:bg-dark-bg dark:border-gray-600 dark:text-white text-left ltr font-mono"
                           :value="format(rawPrice)" @input="update($event)" placeholder="50,000">
                    <input type="hidden" name="generated_amount" :value="rawPrice">
                </div>

                {{-- Service Type --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">برای کدام سرویس؟</label>
                    <select name="generated_service_type" class="w-full px-3 py-2 border rounded-lg dark:bg-dark-bg dark:border-gray-600 dark:text-white">
                        @foreach(\App\Models\RedeemCode::SERVICES as $key => $label)
                            <option value="{{ $key }}" @selected(old('generated_service_type', $gift->generated_service_type ?? '') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Access Level --}}
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">سطح دسترسی</label>
                    <select name="generated_access_level" class="w-full px-3 py-2 border rounded-lg dark:bg-dark-bg dark:border-gray-600 dark:text-white">
                        <option value="exclusive" @selected(old('generated_access_level', $gift->generated_access_level ?? '') == 'exclusive')>اختصاصی (فقط خریدار می‌تواند استفاده کند)</option>
                        <option value="shareable" @selected(old('generated_access_level', $gift->generated_access_level ?? '') == 'shareable')>قابل اشتراک (کد عمومی است)</option>
                    </select>
                </div>
            </div>
        </div>

        <div x-show="giftType === 'app'" class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">نام یا شناسه اپلیکیشن</label>
            <input type="text" name="payload" 
                   x-bind:disabled="giftType !== 'app'"
                   value="{{ old('payload', ($gift->type ?? '') == 'app' ? $gift->payload : '') }}"
                   class="w-full px-3 py-2 border rounded-lg dark:bg-dark-bg dark:border-gray-600 dark:text-white" 
                   placeholder="مثال: com.sibaneh.game">
        </div>

        <div x-show="giftType === 'dynamic_app'" class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg border border-yellow-200 dark:border-yellow-700">
            <div class="flex items-center gap-2 text-yellow-800 dark:text-yellow-300">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span class="text-sm font-bold">هدیه هوشمند (Smart Track)</span>
            </div>
            <p class="text-sm mt-1 text-yellow-700 dark:text-yellow-400">
                اپلیکیشنی که کاربر برای خرید آن اقدام کرده است به صورت خودکار شناسایی و هدیه داده می‌شود.
                <br><span class="text-xs opacity-75">(در حال حاضر در فرآیند خرید پیاده‌سازی نشده است)</span>
            </p>
        </div>

        <div x-show="giftType === 'custom'" class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">توضیحات هدیه</label>
            <textarea name="payload" rows="3" 
                      x-bind:disabled="giftType !== 'custom'"
                      class="w-full px-3 py-2 border rounded-lg dark:bg-dark-bg dark:border-gray-600 dark:text-white" 
                      placeholder="توضیحات...">{{ old('payload', ($gift->type ?? '') == 'custom' ? $gift->payload : '') }}</textarea>
        </div>
    </div>

    <div class="flex items-center">
        <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $gift->is_active ?? true)) class="w-4 h-4 text-purple-600 rounded">
        <label for="is_active" class="mr-2 text-sm text-gray-700 dark:text-gray-300">هدیه فعال باشد</label>
    </div>

    <div class="flex justify-end pt-4">
        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-lg shadow-md transition-colors">
            ذخیره هدیه
        </button>
    </div>
</div>