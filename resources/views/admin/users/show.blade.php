@extends('admin.layouts.app')
@section('title', 'جزئیات مشتری: ' . $user->name)

@section('content')
<div dir="rtl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold dark:text-white">پروفایل مشتری: {{ $user->name ?? '---' }}</h1>
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-dark-hover transition-colors">
            &larr; بازگشت به لیست مشتریان
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-1 space-y-8">
            <div class="bg-white dark:bg-dark-paper shadow-md rounded-lg p-6 transition-colors">
                <h2 class="text-xl font-semibold mb-4 dark:text-white">اطلاعات پایه</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">نام:</span>
                        <span class="font-medium dark:text-gray-200">{{ $user->name ?? '---' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">موبایل:</span>
                        <span class="font-medium dark:text-gray-200" dir="ltr">{{ $user->mobile_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">ایمیل:</span>
                        <span class="font-medium dark:text-gray-200">{{ $user->email ?? '---' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">تاریخ عضویت:</span>
                        <span class="font-medium dark:text-gray-200">{{ jdate($user->created_at)->format('Y/m/d') }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-dark-paper shadow-md rounded-lg p-6 transition-colors">
                <h2 class="text-xl font-semibold mb-4 dark:text-white">آمار</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">مجموع سفارشات:</span>
                        <span class="font-medium text-blue-600 dark:text-blue-400">{{ number_format($totalOrders) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">مجموع خرید (قطعی):</span>
                        <span class="font-medium text-green-600 dark:text-green-400">{{ number_format($totalSpent) }} تومان</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-dark-paper shadow-md rounded-lg p-6 transition-colors">
                <h2 class="text-xl font-semibold mb-4 dark:text-white">آدرس‌های ذخیره شده ({{ $addresses->count() }})</h2>
                <div class="space-y-4">
                    @forelse($addresses as $address)
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-2">
                            <p class="font-medium dark:text-gray-200">{{ $address->full_name }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $address->address_line_1 }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $address->city }}، {{ $address->state }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-500" dir="ltr">{{ $address->phone }}</p>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-500 text-sm">این کاربر هیچ آدرس ذخیره‌شده‌ای ندارد.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-dark-paper shadow-md rounded-lg overflow-hidden transition-colors">
                <h2 class="text-xl font-semibold p-6 dark:text-white">تاریخچه سفارشات</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 dark:bg-dark-hover">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500 dark:text-gray-300">شماره سفارش</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500 dark:text-gray-300">تاریخ</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500 dark:text-gray-300">وضعیت</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500 dark:text-gray-300">مبلغ کل</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($orders as $order)
                                <tr class="hover:bg-gray-50 dark:hover:bg-dark-hover transition-colors">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $order->order_code ?? "#".$order->id }}</td>
                                    
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ jdate($order->created_at)->format('Y/m/d') }}</td>
                                    
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            @switch($order->status)
                                                @case('pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300 @break
                                                @case('processing') bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300 @break
                                                @case('shipped') bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300 @break
                                                @case('delivered') bg-green-200 text-green-900 dark:bg-green-800/50 dark:text-green-200 @break
                                                @case('cancelled') bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300 @break
                                            @endswitch">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    
                                    <td class="px-6 py-4 text-gray-900 dark:text-gray-200">{{ number_format($order->total) }} تومان</td>
                                    
                                    <td class="px-6 py-4 text-left">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 text-sm transition-colors">
                                            (مشاهده)
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-gray-500 dark:text-gray-400">
                                        این کاربر هنوز سفارشی ثبت نکرده است.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4">
                    {{ $orders->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection