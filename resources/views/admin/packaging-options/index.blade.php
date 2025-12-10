@extends('admin.layouts.app')
@section('title', 'مدیریت انواع بسته‌بندی')

@section('content')
<div dir="rtl">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold dark:text-white">مدیریت انواع بسته‌بندی</h1>
        <a href="{{ route('admin.packaging-options.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            + افزودن گزینه جدید
        </a>
    </div>

    <div class="bg-white dark:bg-dark-paper shadow-md rounded-lg overflow-hidden transition-colors">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">تصویر</th> 
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">نام</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">هزینه (تومان)</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">وضعیت</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover">عملیات</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 dark:text-gray-300">
                @forelse ($packagingOptions as $option)
                <tr class="hover:bg-gray-50 dark:hover:bg-dark-hover transition-colors">
                    
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-dark-paper text-sm">
                        @if($option->image_path)
                            <img src="{{ Storage::url($option->image_path) }}" alt="{{ $option->name }}" class="w-16 h-16 object-cover rounded-md border dark:border-gray-600">
                        @else
                            <span class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-md flex items-center justify-center text-xs text-gray-500 dark:text-gray-400">بدون تصویر</span>
                        @endif
                    </td>
                    
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-dark-paper text-sm text-gray-900 dark:text-white">{{ $option->name }}</td>
                    
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-dark-paper text-sm text-gray-900 dark:text-white">{{ number_format($option->price) }} تومان</td>
                    
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-dark-paper text-sm">
                        @if($option->is_active)
                            <span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full dark:bg-green-900/50 dark:text-green-300">فعال</span>
                        @else
                            <span class="px-2 py-1 font-semibold leading-tight text-gray-700 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-300">غیرفعال</span>
                        @endif
                    </td>
                    
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-dark-paper text-sm text-left">
                        <a href="{{ route('admin.packaging-options.edit', $option) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">ویرایش</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-10 text-center text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-dark-paper"> 
                        هیچ گزینه‌ی بسته‌بندی تعریف نشده است.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection