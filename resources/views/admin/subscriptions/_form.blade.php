@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6 text-right">
        <strong class="font-bold">خطا!</strong>
        <ul class="mt-2 list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-right mb-2">نام اشتراک</label>
            <input type="text" name="name" value="{{ old('name', $subscription->name ?? '') }}" 
                   class="block w-full px-4 py-2.5 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-right mb-2">قیمت (تومان)</label>
            <input type="number" name="price" value="{{ old('price', $subscription->price ?? '') }}" 
                   class="block w-full px-4 py-2.5 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white" required>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-right mb-2">مدت اعتبار (روز)</label>
            <input type="number" name="duration_in_days" value="{{ old('duration_in_days', $subscription->duration_in_days ?? 30) }}" 
                   class="block w-full px-4 py-2.5 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-right mb-2">انتخاب هدیه (اختیاری)</label>
            <select name="gift_id" class="block w-full px-4 py-2.5 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white">
                <option value="">-- بدون هدیه --</option>
                @foreach($gifts as $g)
                    <option value="{{ $g->id }}" @selected(old('gift_id', $subscription->gift_id ?? '') == $g->id)>
                        {{ $g->title }}
                    </option>
                @endforeach
            </select>
            <p class="text-xs text-gray-500 mt-1">کاربر با خرید این اشتراک، این هدیه را دریافت می‌کند.</p>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-right mb-2">توضیحات اشتراک</label>
        <textarea name="description" rows="3" 
                  class="block w-full px-4 py-2.5 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white"
                  placeholder="توضیحاتی که به کاربر نمایش داده می‌شود...">{{ old('description', $subscription->description ?? '') }}</textarea>
    </div>

    <div class="flex items-center">
        <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $subscription->is_active ?? true)) class="w-4 h-4 text-blue-600 rounded">
        <label for="is_active" class="mr-2 text-sm font-semibold text-gray-700 dark:text-gray-300">این پلن فعال باشد</label>
    </div>

    <div class="flex justify-end pt-4">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md">
            ذخیره اطلاعات
        </button>
    </div>
</div>