@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6 text-right shadow-sm">
        <strong class="font-bold">خطا!</strong>
        <ul class="mt-2 list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div x-data="{ 
    creationType: '{{ old('creation_type', isset($redeemCode) ? 'single' : 'single') }}',
    isEditMode: {{ isset($redeemCode) ? 'true' : 'false' }}
}">

    {{-- انتخاب حالت فقط در زمان ایجاد فعال است --}}
    <div x-show="!isEditMode" class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">روش ایجاد کد:</label>
        <div class="flex space-x-4 space-x-reverse">
            <label class="flex items-center cursor-pointer">
                <input type="radio" name="creation_type" value="single" x-model="creationType" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                <span class="mr-2 text-gray-700 dark:text-gray-300">کد تکی (دستی)</span>
            </label>
            <label class="flex items-center cursor-pointer">
                <input type="radio" name="creation_type" value="bulk" x-model="creationType" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                <span class="mr-2 text-gray-700 dark:text-gray-300">تولید انبوه (تصادفی)</span>
            </label>
        </div>
    </div>
    
    {{-- اگر در حالت ادیت باشیم، اینپوت مخفی برای تایپ ارسال می‌کنیم --}}
    <div x-show="isEditMode">
        <input type="hidden" name="creation_type" value="single">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        
        {{-- 1. ورودی کد (فقط در حالت تکی) --}}
        <div x-show="creationType === 'single'">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-right mb-2">کد مورد نظر</label>
            <input type="text" name="code" value="{{ old('code', $redeemCode->code ?? '') }}" 
                   class="block w-full px-4 py-2.5 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white font-mono text-left ltr"
                   placeholder="OFF-SUMMER"
                   :required="creationType === 'single'">
        </div>

        {{-- 2. تنظیمات تولید انبوه (فقط در حالت بالک) --}}
        <div x-show="creationType === 'bulk'" class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-right mb-2">تعداد کد</label>
                <input type="number" name="quantity" min="1" max="1000"
                       class="block w-full px-4 py-2.5 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white"
                       placeholder="مثال: 50"
                       :required="creationType === 'bulk'">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-right mb-2">پیشوند (اختیاری)</label>
                <input type="text" name="prefix" 
                       class="block w-full px-4 py-2.5 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white font-mono text-left ltr"
                       placeholder="PRO">
            </div>
        </div>

        {{-- 3. سرویس مجاز --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-right mb-2">سرویس مجاز</label>
            <select name="service_type" class="block w-full px-4 py-2.5 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white">
                @foreach(\App\Models\RedeemCode::SERVICES as $key => $label)
                    <option value="{{ $key }}" @selected(old('service_type', $redeemCode->service_type ?? '') == $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        
        {{-- 4. مبلغ اعتبار (با جداکننده ۳ رقمی) --}}
        <div x-data="{ 
            rawPrice: '{{ old('amount', $redeemCode->amount ?? '') }}',
            format(value) {
                if (!value) return '';
                return value.toString().replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            },
            update(event) {
                let val = event.target.value.replace(/[^0-9]/g, '');
                this.rawPrice = val;
                event.target.value = this.format(val);
            }
        }">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-right mb-2">مبلغ اعتبار / ارزش (تومان)</label>
            <input 
                type="text" 
                inputmode="numeric" 
                class="block w-full px-4 py-2.5 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white text-left font-mono"
                :value="format(rawPrice)"
                @input="update($event)"
                required
                placeholder="مثال: 50,000"
            >
            <input type="hidden" name="amount" :value="rawPrice">
        </div>

        {{-- 5. تاریخ انقضا --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-right mb-2">تاریخ انقضا (اختیاری)</label>
            <input type="datetime-local" name="expires_at" value="{{ old('expires_at', isset($redeemCode) && $redeemCode->expires_at ? $redeemCode->expires_at->format('Y-m-d\TH:i') : '') }}" 
                   class="block w-full px-4 py-2.5 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white ltr">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        {{-- 6. تعداد دفعات استفاده --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-right mb-2">تعداد مجاز استفاده</label>
            <input type="number" name="usage_limit" value="{{ old('usage_limit', $redeemCode->usage_limit ?? 1) }}" min="1"
                   class="block w-full px-4 py-2.5 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white" required>
            <p class="text-xs text-gray-500 mt-1">معمولاً برای کدهای یکبار مصرف، عدد ۱ وارد می‌شود.</p>
        </div>

        {{-- 7. وضعیت فعال --}}
        <div class="flex items-center pt-8">
            <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $redeemCode->is_active ?? true)) 
                   class="w-5 h-5 text-blue-600 rounded cursor-pointer">
            <label for="is_active" class="mr-2 text-sm font-semibold text-gray-700 dark:text-gray-300 cursor-pointer select-none">
                این کد فعال باشد
            </label>
        </div>
    </div>

    <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition-colors">
            {{ isset($redeemCode) ? 'ویرایش کد' : 'ایجاد کد(ها)' }}
        </button>
    </div>
</div>