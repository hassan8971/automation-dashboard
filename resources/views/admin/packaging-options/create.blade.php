@extends('admin.layouts.app')
@section('title', 'افزودن گزینه بسته‌بندی')

@section('content')
<div dir="rtl">
    <h1 class="text-3xl font-bold mb-6 dark:text-white">افزودن گزینه بسته‌بندی جدید</h1>

    <form action="{{ route('admin.packaging-options.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="bg-white dark:bg-dark-paper shadow-md rounded-lg p-6 space-y-6 transition-colors">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">نام</label>
                <input type="text" name="name" id="name" 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-dark-bg dark:border-gray-600 dark:text-white transition-colors" 
                       placeholder="مثال: بسته‌بندی هدیه" required>
            </div>

            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">تصویر (اختیاری)</label>
                <input type="file" name="image" id="image" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                    file:mr-4 file:py-2 file:px-4 file:ml-4
                    file:rounded-full file:border-0
                    file:text-sm file:font-semibold
                    file:bg-blue-50 file:text-blue-700
                    hover:file:bg-blue-100
                    dark:file:bg-blue-900/50 dark:file:text-blue-300
                    dark:hover:file:bg-blue-900
                    transition-colors">
                @error('image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                
                @if(isset($option) && $option->image_path)
                <div class="mt-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">تصویر فعلی:</p>
                    <img src="{{ Storage::url($option->image_path) }}" alt="{{ $option->name }}" class="w-24 h-24 object-cover rounded-md border dark:border-gray-600">
                </div>
                @endif
            </div>

            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">هزینه (به تومان)</label>
                <input type="number" name="price" id="price" 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-dark-bg dark:border-gray-600 dark:text-white transition-colors" 
                       value="0" required>
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" 
                       class="h-4 w-4 text-blue-600 border-gray-300 rounded ml-2 dark:bg-dark-bg dark:border-gray-600 dark:checked:bg-blue-600 transition-colors" 
                       checked>
                <label for="is_active" class="text-sm font-medium text-gray-700 dark:text-gray-300">این گزینه فعال باشد</label>
            </div>
            
            <div class="flex justify-end">
                <a href="{{ route('admin.packaging-options.index') }}" class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-dark-hover transition-colors">انصراف</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 mr-2 transition-colors">ذخیره</button>
            </div>
        </div>
    </form>
</div>
@endsection