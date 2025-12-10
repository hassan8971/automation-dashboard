@extends('admin.layouts.app')
@section('title', 'مدیریت منابع خرید')

@section('content')
<div dir="rtl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold dark:text-white">مدیریت منابع خرید</h1>
        <a href="{{ route('admin.buy-sources.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            + افزودن منبع جدید
        </a>
    </div>
    
    @if(session('success'))
        <div class="bg-green-100 border-r-4 border-green-500 text-green-700 dark:bg-green-900/50 dark:text-green-300 dark:border-green-600 p-4 mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border-r-4 border-red-500 text-red-700 dark:bg-red-900/50 dark:text-red-300 dark:border-red-600 p-4 mb-4" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white dark:bg-dark-paper shadow-md rounded-lg overflow-hidden transition-colors">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">نام منبع</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">تلفن</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">شهر</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover"></th>
                </tr>
            </thead>
            <tbody class="text-gray-700 dark:text-gray-300">
                @forelse ($sources as $source)
                <tr class="hover:bg-gray-50 dark:hover:bg-dark-hover border-b border-gray-200 dark:border-gray-700 transition-colors">
                    <td class="px-5 py-5 text-sm">{{ $source->name }}</td>
                    <td class="px-5 py-5 text-sm">{{ $source->phone ?? '---' }}</td>
                    <td class="px-5 py-5 text-sm">{{ $source->city ?? '---' }}</td>
                    <td class="px-5 py-5 text-sm text-left">
                        <a href="{{ route('admin.buy-sources.edit', $source) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 ml-4 transition-colors">ویرایش</a>
                        <form action="{{ route('admin.buy-sources.destroy', $source) }}" method="POST" class="inline-block" onsubmit="return confirm('آیا از حذف این منبع مطمئن هستید؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors">حذف</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-10 text-center text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                        هیچ منبع خریدی تعریف نشده است.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection