<div class="bg-white dark:bg-dark-paper shadow-md rounded-lg p-6 space-y-6 transition-colors">
    
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">نام (متن لینک)</label>
        <input type="text" name="name" id="name" value="{{ old('name', $menuItem->name) }}" 
               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-dark-bg dark:border-gray-600 dark:text-white transition-colors" 
               placeholder="مثال: صفحه اصلی" required>
        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
    
    <div>
        <label for="link_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">لینک (URL یا Slug)</label>
        <input type="text" name="link_url" id="link_url" value="{{ old('link_url', $menuItem->link_url) }}" 
               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-dark-bg dark:border-gray-600 dark:text-white transition-colors" 
               placeholder="مثال: / یا /categories/clothing" required dir="ltr">
        @error('link_url') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label for="parent_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">والد (برای منوی تودرتو)</label>
            <select name="parent_id" id="parent_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-dark-bg dark:border-gray-600 dark:text-white transition-colors">
                <option value="">-- بدون والد (آیتم اصلی) --</option>
                @foreach ($menuItems as $item)
                    <option value="{{ $item->id }}" 
                        @if(isset($menuItem->parent_id))
                            @selected(old('parent_id', $menuItem->parent_id) == $item->id)
                        @endif>
                        {{ $item->name }} ({{ $item->menu_group }})
                    </option>
                @endforeach
            </select>
            @error('parent_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        
        <div>
            <label for="menu_group" class="block text-sm font-medium text-gray-700 dark:text-gray-300">گروه منو</label>
            <select name="menu_group" id="menu_group" class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-dark-bg dark:border-gray-600 dark:text-white transition-colors">
                <option value="main_header" @selected(old('menu_group', $menuItem->menu_group) == 'main_header')>هدر اصلی</option>
                <option value="footer_links" @selected(old('menu_group', $menuItem->menu_group) == 'footer_links')>لینک‌های فوتر</option>
            </select>
            @error('menu_group') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        
        <div>
            <label for="order" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ترتیب نمایش</label>
            <input type="number" name="order" id="order" value="{{ old('order', $menuItem->order) }}" 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-dark-bg dark:border-gray-600 dark:text-white transition-colors" 
                   required>
            @error('order') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">ذخیره</button>
    </div>
</div>