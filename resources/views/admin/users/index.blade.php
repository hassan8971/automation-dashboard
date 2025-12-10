@extends('admin.layouts.app')
@section('title', 'مدیریت مشتریان')

@section('content')
<div dir="rtl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold dark:text-white">مدیریت مشتریان</h1>
    </div>

    <div class="mb-4">
        <form method="GET" action="{{ route('admin.users.index') }}">
            <div class="flex rounded-md shadow-sm">
                <input type="text" name="search" value="{{ request('search') }}"
                       class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border-gray-300 dark:border-gray-600 dark:bg-dark-bg dark:text-white dark:placeholder-gray-400 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="جستجو بر اساس نام، ایمیل یا موبایل...">
                
                <button type="submit"
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-gray-50 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-l-md dark:bg-dark-hover dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700 transition-colors">
                    جستجو
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white dark:bg-dark-paper shadow-md rounded-lg overflow-hidden transition-colors">
        <table class="min-w-full leading-normal">
            <thead class="bg-gray-100 dark:bg-dark-hover">
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">نام</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">ایمیل</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">موبایل</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">تاریخ عضویت</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                <tr class="hover:bg-gray-50 dark:hover:bg-dark-hover transition-colors">
                    
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 text-sm">
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $user->name ?? '---' }}</p>
                    </td>
                    
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 text-sm text-gray-900 dark:text-gray-300">
                        {{ $user->email ?? '---' }}
                    </td>
                    
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 text-sm text-gray-900 dark:text-gray-300" dir="ltr">
                        {{ $user->mobile }}
                    </td>
                    
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 text-sm text-gray-900 dark:text-gray-300">
                        {{ $user->created_at ? jdate($user->created_at)->format('Y/m/d') : '---' }}
                    </td>
                    
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 text-sm text-left">
                        <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                            مشاهده جزئیات
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-10 text-center text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                        مشتری با این مشخصات یافت نشد.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="p-4">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection