<div class="bg-white dark:bg-dark-paper shadow-md rounded-lg p-6 space-y-6 transition-colors">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">نام سایز</label>
        
        <input type="text" name="name" id="name" value="{{ old('name', $size->name) }}" 
               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-dark-bg dark:border-gray-600 dark:text-white dark:placeholder-gray-400 transition-colors" 
               placeholder="مثال: 38.5 یا Medium" required>
        
        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
    
    <div class="flex justify-end">
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">ذخیره</button>
    </div>
</div>