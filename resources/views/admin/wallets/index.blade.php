@extends('admin.layouts.app')

@section('title', 'مدیریت کیف پول‌ها')

@section('content')
<div dir="rtl">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h1 class="text-3xl font-bold dark:text-white">کیف پول کاربران</h1>
        
        {{-- فرم جستجو --}}
        <form action="{{ route('admin.wallets.index') }}" method="GET" class="w-full md:w-1/3 flex gap-2">
            <div class="relative w-full">
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="w-full px-4 py-2 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500" 
                       placeholder="جستجو (نام، ایمیل، موبایل)...">
                
                @if(request('search'))
                    <a href="{{ route('admin.wallets.index') }}" class="absolute left-2 top-2.5 text-gray-400 hover:text-red-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </a>
                @endif
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow transition-colors">
                جستجو
            </button>
        </form>
    </div>
    @if(session('success'))
        <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 mb-4 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 mb-4 rounded">{{ session('error') }}</div>
    @endif

    <div class="bg-white dark:bg-dark-paper shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead class="bg-gray-100 dark:bg-dark-hover">
                <tr>
                    <th class="px-5 py-3 border-b-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">کاربر</th>
                    <th class="px-5 py-3 border-b-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">موجودی (تومان)</th>
                    <th class="px-5 py-3 border-b-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">آخرین بروزرسانی</th>
                    <th class="px-5 py-3 border-b-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">سرویس</th>
                    <th class="px-5 py-3 border-b-2 text-left">عملیات</th>
                </tr>
            </thead>
            <tbody class="dark:text-gray-200">
                @foreach($wallets as $wallet)
                <tr class="hover:bg-gray-50 dark:hover:bg-dark-hover">
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        <a href="users/{{$wallet->user->id}}">
                            <div class="font-bold">{{ $wallet->user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $wallet->user->mobile }}</div>
                        </a>
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-emerald-600 font-bold text-lg">{{ number_format($wallet->balance) }}</span>
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 text-sm">
                        {{ $wallet->updated_at->diffForHumans() }}
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        <span class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded text-xs font-bold">
                            {{ strtoupper($wallet->service_name) }}
                        </span>
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 text-left">
                        <a href="{{ route('admin.wallets.show', $wallet) }}" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 text-sm">مشاهده تراکنش‌ها و مدیریت</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">
            {{ $wallets->links() }}
        </div>
    </div>
</div>
@endsection