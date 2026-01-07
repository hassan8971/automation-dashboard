<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">عنوان تب</label>
        <input type="text" name="title" value="{{ old('title', $appTab->title ?? '') }}" 
               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-dark-bg dark:text-white p-2.5 focus:ring-blue-500"
               placeholder="مثلا: خانه" required>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">لینک مقصد (Slug)</label>
        <input type="text" name="link" value="{{ old('link', $appTab->link ?? '') }}" 
               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-dark-bg dark:text-white p-2.5 dir-ltr text-left font-mono focus:ring-blue-500"
               placeholder="home" required>
        <p class="text-[10px] text-gray-500 mt-1">این لینک باید با Slug یکی از صفحات (App Pages) برابر باشد.</p>
    </div>

    <div class="md:col-span-2 bg-gray-50 dark:bg-dark-hover p-4 rounded-xl border dark:border-gray-700" 
         x-data="{ 
             iconType: '{{ (isset($appTab) && $appTab->image_path) ? 'image' : 'font' }}',
             fontIcon: '{{ old('icon', $appTab->icon ?? 'fas fa-home') }}'
         }">
        
        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">نوع آیکون نمایش:</label>
        
        <div class="flex gap-4 mb-4">
            <label class="flex items-center cursor-pointer">
                <input type="radio" name="icon_type_selector" value="font" x-model="iconType" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                <span class="mr-2 text-sm text-gray-700 dark:text-gray-300">استفاده از فونت آیکون (FontAwesome)</span>
            </label>
            <label class="flex items-center cursor-pointer">
                <input type="radio" name="icon_type_selector" value="image" x-model="iconType" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                <span class="mr-2 text-sm text-gray-700 dark:text-gray-300">آپلود تصویر اختصاصی</span>
            </label>
        </div>

        <div x-show="iconType === 'font'" x-transition>
            <label class="block text-xs font-medium text-gray-500 mb-1">کلاس آیکون</label>
            <div class="flex gap-2">
                <input type="text" name="icon" x-model="fontIcon"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-dark-bg dark:text-white p-2.5 dir-ltr text-left font-mono focus:ring-blue-500"
                       placeholder="fas fa-home">
                <div class="w-11 h-11 bg-white dark:bg-dark-bg rounded-lg flex items-center justify-center text-xl text-blue-600 border dark:border-gray-600 shadow-sm">
                    <i :class="fontIcon"></i>
                </div>
            </div>
            <a href="https://fontawesome.com/v5/search?m=free" target="_blank" class="text-xs text-blue-500 hover:underline mt-1 inline-block">مشاهده لیست آیکون‌ها</a>
        </div>

        <div x-show="iconType === 'image'" x-transition>
            <label class="block text-xs font-medium text-gray-500 mb-1">آپلود فایل آیکون</label>
            <div class="flex items-start gap-4">
                <div class="flex-1">
                    <input type="file" name="image" accept="image/png, image/jpeg, image/svg+xml"
                           class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white dark:text-gray-400 focus:outline-none dark:bg-dark-bg dark:border-gray-600 dark:placeholder-gray-400">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        فرمت‌های مجاز: PNG, JPG, SVG. 
                        <span class="text-blue-600 font-bold dir-ltr inline-block">(سایز استاندارد: 48x48 یا 64x64 پیکسل)</span>
                    </p>
                </div>
                
                @if(isset($appTab) && $appTab->image_path)
                    <div class="text-center">
                        <span class="block text-[10px] text-gray-400 mb-1">آیکون فعلی</span>
                        <img src="{{ asset('storage/' . $appTab->image_path) }}" class="w-10 h-10 object-contain bg-white rounded border p-1">
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ترتیب نمایش</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $appTab->sort_order ?? 0) }}" 
               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-dark-bg dark:text-white p-2.5 focus:ring-blue-500">
    </div>

    <div class="flex items-center h-full pt-6">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $appTab->is_active ?? true) ? 'checked' : '' }} class="rounded text-blue-600 w-5 h-5 focus:ring-blue-500 border-gray-300">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">این تب فعال باشد.</span>
        </label>
    </div>
</div>

<div class="mt-6 border-t dark:border-gray-700 pt-4 flex justify-end">
    <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow transition">
        <i class="fas fa-save ml-1"></i> ذخیره تغییرات
    </button>
</div>