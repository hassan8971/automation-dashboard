@extends('admin.layouts.app')

@section('title', 'مدیریت گیفت ها')

@section('content')
<div dir="rtl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold dark:text-white">گیفت ها</h1>
        <a href="{{ route('admin.gifts.create') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors shadow-lg">
            + تعریف گیفت جدید
        </a>
    </div>

    <div class="bg-white dark:bg-dark-paper shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead class="bg-gray-100 dark:bg-dark-hover">
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">عنوان</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">نوع گیفت</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">وضعیت</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700"></th>
                </tr>
            </thead>
            <tbody class="dark:text-gray-200">
                @foreach($gifts as $gift)
                <tr class="hover:bg-gray-50 dark:hover:bg-dark-hover transition-colors">
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">{{ $gift->title }}</td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">
                            {{ $gift->type }}
                        </span>
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        @if($gift->is_active)
                            <span class="relative inline-block px-3 py-1 font-semibold text-green-900 dark:text-green-200 leading-tight">
                                <span aria-hidden class="absolute inset-0 bg-green-200 dark:bg-green-900 opacity-50 rounded-full"></span>
                                <span class="relative">فعال</span>
                            </span>
                        @else
                            <span class="relative inline-block px-3 py-1 font-semibold text-red-900 dark:text-red-200 leading-tight">
                                <span aria-hidden class="absolute inset-0 bg-red-200 dark:bg-red-900 opacity-50 rounded-full"></span>
                                <span class="relative">غیرفعال</span>
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 text-left whitespace-nowrap">
                        <a href="{{ route('admin.gifts.edit', $gift) }}" class="text-blue-600 hover:text-blue-800 ml-3 text-sm">ویرایش</a>
                        
                        <form action="{{ route('admin.gifts.destroy', $gift) }}" method="POST" class="inline-block" onsubmit="return confirm('آیا از حذف این گیفت مطمئن هستید؟')">
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