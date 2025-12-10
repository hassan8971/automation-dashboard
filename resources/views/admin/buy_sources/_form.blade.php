<div class="bg-white dark:bg-dark-paper shadow-md rounded-lg p-6 space-y-6 transition-colors">
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">نام منبع (اجباری)</label>
            <input type="text" name="name" id="name" value="{{ old('name', $source->name) }}" 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-dark-bg dark:border-gray-600 dark:text-white dark:placeholder-gray-400 transition-colors" 
                   placeholder="مثال: تأمین‌کننده X" required>
            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">شماره تلفن (اختیاری)</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', $source->phone) }}" 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-dark-bg dark:border-gray-600 dark:text-white transition-colors">
            @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">شهر (اختیاری)</label>
            <input type="text" name="city" id="city" value="{{ old('city', $source->city) }}" 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-dark-bg dark:border-gray-600 dark:text-white transition-colors">
            @error('city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
    </div>

    <div>
        <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">آدرس (اختیاری)</label>
        <textarea name="address" id="address" rows="3" 
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-dark-bg dark:border-gray-600 dark:text-white transition-colors">{{ old('address', $source->address) }}</textarea>
        @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">توضیحات (اختیاری)</label>
        <textarea name="description" id="description" rows="3" 
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-dark-bg dark:border-gray-600 dark:text-white transition-colors">{{ old('description', $source->description) }}</textarea>
        @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div class="flex justify-end">
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">ذخیره</button>
    </div>
</div>