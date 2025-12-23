@extends('admin.layouts.app')

@section('title', 'مدیریت اپلیکیشن‌ها')

@section('content')
<div dir="rtl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold dark:text-white">اپلیکیشن‌ها (محصولات)</h1>
        <a href="{{ route('admin.products.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-lg">
            + اپلیکیشن جدید
        </a>
    </div>

    <div class="bg-white dark:bg-dark-paper shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead class="bg-gray-100 dark:bg-dark-hover">
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">آیکون</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">عنوان</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">نسخه</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">نوع انتشار</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">وضعیت</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-left">عملیات</th>
                </tr>
            </thead>
            <tbody class="dark:text-gray-200">
                @foreach($products as $product)
                <tr class="hover:bg-gray-50 dark:hover:bg-dark-hover transition-colors">
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        @if($product->icon_path)
                            <img src="{{ asset('storage/'.$product->icon_path) }}" class="w-10 h-10 rounded-lg shadow-sm">
                        @else
                            <div class="w-10 h-10 bg-gray-200 rounded-lg"></div>
                        @endif
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="font-bold">{{ $product->title }}</div>
                        <div class="text-xs text-gray-500">{{ $product->category->name ?? 'بدون دسته' }}</div>
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        <span class="font-mono text-sm">{{ $product->version }}</span>
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex gap-1">
                            @if($product->type_pwa) <span class="px-2 py-0.5 text-xs bg-orange-100 text-orange-800 rounded">PWA</span> @endif
                            @if($product->type_internal) <span class="px-2 py-0.5 text-xs bg-purple-100 text-purple-800 rounded">Internal</span> @endif
                            @if($product->type_appstore) <span class="px-2 py-0.5 text-xs bg-blue-100 text-blue-800 rounded">AppStore</span> @endif
                        </div>
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        @if($product->availability == 'available')
                            <span class="text-green-600 text-xs font-bold">موجود</span>
                        @else
                            <span class="text-red-500 text-xs">ناموجود</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 text-left whitespace-nowrap">
                        <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:text-blue-800 ml-3 text-sm">ویرایش</a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline-block" onsubmit="return confirm('حذف شود؟')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700 text-sm">حذف</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection