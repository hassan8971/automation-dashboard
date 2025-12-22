@extends('admin.layouts.app')

@section('title', 'مدیریت افزونه‌ها')

@section('content')
<div dir="rtl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold dark:text-white">افزونه‌ها (Add-ons)</h1>
        <a href="{{ route('admin.addons.create') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors shadow-lg">
            + ایجاد افزونه جدید
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-r-4 border-green-500 text-green-700 dark:bg-green-900/50 dark:text-green-300 p-4 mb-4 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-dark-paper shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead class="bg-gray-100 dark:bg-dark-hover">
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-sm font-semibold text-gray-600 dark:text-gray-300">نام</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-sm font-semibold text-gray-600 dark:text-gray-300">قیمت</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-sm font-semibold text-gray-600 dark:text-gray-300">مدت</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-sm font-semibold text-gray-600 dark:text-gray-300">وضعیت</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-left">عملیات</th>
                </tr>
            </thead>
            <tbody class="dark:text-gray-200">
                @foreach($addons as $addon)
                <tr class="hover:bg-gray-50 dark:hover:bg-dark-hover transition-colors">
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 font-medium">{{ $addon->name }}</td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 text-emerald-600">{{ number_format($addon->price) }} تومان</td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">{{ $addon->duration_in_days }} روز</td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        <span class="px-2 py-1 text-xs font-semibold leading-tight {{ $addon->is_active ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100' }} rounded-full">
                            {{ $addon->is_active ? 'فعال' : 'غیرفعال' }}
                        </span>
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 text-left whitespace-nowrap">
                        <a href="{{ route('admin.addons.edit', $addon) }}" class="text-blue-600 hover:text-blue-800 ml-3 text-sm">ویرایش</a>
                        <form action="{{ route('admin.addons.destroy', $addon) }}" method="POST" class="inline-block" onsubmit="return confirm('حذف شود؟')">
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