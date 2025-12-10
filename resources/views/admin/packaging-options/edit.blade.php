@extends('admin.layouts.app')
@section('title', 'ویرایش گزینه بسته‌بندی')

@section('content')
<div dir="rtl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold dark:text-white">ویرایش: {{ $option->name }}</h1>
        <a href="{{ route('admin.packaging-options.index') }}" class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-dark-hover transition-colors">
            &larr; بازگشت
        </a>
    </div>

    <form action="{{ route('admin.packaging-options.update', $option) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT') 
        <div class="bg-white dark:bg-dark-paper shadow-md rounded-lg p-6 space-y-6 transition-colors">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">نام</label>
                <input type="text" name="name" id="name" value="{{ old('name', $option->name) }}" 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-dark-bg dark:border-gray-600 dark:text-white transition-colors" 
                       required>
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">هزینه (به تومان)</label>
                <input type="number" name="price" id="price" value="{{ old('price', $option->price) }}" 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-dark-bg dark:border-gray-600 dark:text-white transition-colors" 
                       required>
                @error('price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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
                
                @if($option->image_path)
                <div class="mt-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">تصویر فعلی:</p>
                    <img src="{{ Storage::url($option->image_path) }}" alt="{{ $option->name }}" class="w-24 h-24 object-cover rounded-md border dark:border-gray-600">
                </div>
                @endif
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" 
                       class="h-4 w-4 text-blue-600 border-gray-300 rounded ml-2 dark:bg-dark-bg dark:border-gray-600 dark:checked:bg-blue-600 transition-colors" 
                       @checked(old('is_active', $option->is_active))>
                <label for="is_active" class="text-sm font-medium text-gray-700 dark:text-gray-300">این گزینه فعال باشد</label>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">ذخیره تغییرات</button>
            </div>
        </div>
    </form>
</div>
@endsection