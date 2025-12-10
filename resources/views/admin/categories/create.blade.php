@extends('admin.layouts.app')

@section('title', 'افزودن دسته بندی جدید')

@section('content')
    <h1 class="text-2xl font-semibold mb-4 dark:text-white">افزودن دسته بندی جدید</h1>

    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6" dir="rtl">
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">نام دسته‌بندی</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $category->name ?? '') }}" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-dark-paper dark:border-gray-600 dark:text-white transition-colors">
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">اسلاگ (Slug)</label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug', $category->slug ?? '') }}" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-dark-paper dark:border-gray-600 dark:text-white transition-colors" dir="ltr">
                    @error('slug') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label for="parent_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">دسته‌بندی والد</label>
                    <select name="parent_id" id="parent_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-dark-paper dark:border-gray-600 dark:text-white transition-colors">
                        <option value="">-- بدون والد (سطح بالا) --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" 
                                {{ old('parent_id', $category->parent_id ?? '') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_visible" id="is_visible" value="1" 
                           {{ old('is_visible', $category->is_visible ?? true) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 border-gray-300 rounded ml-2 dark:bg-dark-paper dark:border-gray-600 dark:checked:bg-blue-600">
                    <label for="is_visible" class="text-sm font-medium text-gray-700 dark:text-gray-300">قابل مشاهده برای عموم</label>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">تصویر دسته‌بندی</label>
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

                    @if(isset($category) && $category->image_path)
                        <div class="mt-4">
                            <img src="{{ Storage::url($category->image_path) }}" alt="{{ $category->name }}" class="w-32 h-32 object-cover rounded-md border dark:border-gray-600">
                        </div>
                    @endif
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">توضیحات</label>
                    <textarea name="description" id="description" rows="5" 
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-dark-paper dark:border-gray-600 dark:text-white transition-colors">{{ old('description', $category->description ?? '') }}</textarea>
                    @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end" dir="rtl">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                ایجاد دسته
            </button>
        </div>

    </form>
@endsection