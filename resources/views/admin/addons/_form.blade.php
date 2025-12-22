@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 dark:bg-red-900/50 dark:text-red-300 dark:border-red-600 px-4 py-3 rounded relative mb-6 text-right shadow-sm">
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
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-right mb-2">نام افزونه (Add-on)</label>
            <input type="text" name="name" value="{{ old('name', $addon->name ?? '') }}" 
                   class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 text-right dark:bg-dark-paper dark:border-gray-600 dark:text-white" required placeholder="مثال: Arkade Bundle">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-right mb-2">قیمت (تومان)</label>
            <input type="number" name="price" value="{{ old('price', $addon->price ?? '') }}" 
                   class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 text-right dark:bg-dark-paper dark:border-gray-600 dark:text-white" required placeholder="مثال: 50000">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-right mb-2">مدت اعتبار (روز)</label>
        <input type="number" name="duration_in_days" value="{{ old('duration_in_days', $addon->duration_in_days ?? 30) }}" 
               class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 text-right dark:bg-dark-paper dark:border-gray-600 dark:text-white" required>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-right mb-2">اپلیکیشن‌های موجود در این باندل (با کاما , جدا کنید)</label>
        <textarea name="supported_apps" rows="3" 
                  class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 text-right dark:bg-dark-paper dark:border-gray-600 dark:text-white placeholder-gray-400" 
                  placeholder="Arkade, Game1, Game2...">{{ old('supported_apps', isset($addon) && $addon->supported_apps ? implode(', ', $addon->supported_apps) : '') }}</textarea>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-right mb-2">توضیحات (اختیاری)</label>
        <textarea name="description" rows="3" 
                  class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 text-right dark:bg-dark-paper dark:border-gray-600 dark:text-white">{{ old('description', $addon->description ?? '') }}</textarea>
    </div>

    <div class="bg-gray-50 dark:bg-dark-hover rounded-lg p-4 border border-gray-100 dark:border-gray-700 flex items-center">
        <input type="checkbox" name="is_active" id="is_active" value="1" 
               @checked(old('is_active', $addon->is_active ?? true))
               class="h-5 w-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500 dark:bg-dark-bg dark:border-gray-600 dark:checked:bg-purple-600 cursor-pointer">
        <label for="is_active" class="mr-3 block text-sm font-semibold text-gray-700 dark:text-gray-300 cursor-pointer select-none">
            این افزونه فعال باشد
        </label>
    </div>

    <div class="flex justify-end pt-4">
        <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition-transform transform active:scale-95">
            ذخیره اطلاعات
        </button>
    </div>
</div>