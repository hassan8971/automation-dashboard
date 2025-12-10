@extends('admin.layouts.app')

@section('title', 'مدیریت سفارشات')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-gray-800 dark:text-white">مدیریت سفارشات</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 dark:bg-green-900/50 dark:border-green-600 dark:text-green-300 px-4 py-3 rounded relative mb-4 text-right" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-dark-paper shadow-md rounded-lg overflow-hidden transition-colors duration-300">
        <table class="min-w-full">
            <thead class="bg-gray-100 dark:bg-dark-hover">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">شناسه سفارش</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">مشتری</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">تاریخ</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">مجموع</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">وضعیت</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($orders as $order)
                    <tr class="hover:bg-gray-50 dark:hover:bg-dark-hover transition-colors">
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white text-right">
                            #{{ $order->id }}
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-right">
                            <a href="{{route('admin.users.show', $order->user) }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                {{ $order->user->name ?? 'مهمان' }}
                            </a>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-right">
                            <p>تاریخ سفارش: {{ jdate($order->created_at)->format('Y/m/d') }}</p>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right">
                            {{ number_format($order->total) }} تومان
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @switch($order->status)
                                    @case('pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300 @break
                                    @case('processing') bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300 @break
                                    @case('shipped') bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300 @break
                                    @case('completed') bg-green-200 text-green-900 dark:bg-green-800/50 dark:text-green-200 @break
                                    @case('cancelled') bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300 @break
                                @endswitch">
                                @switch($order->status)
                                    @case('pending') در انتظار @break
                                    @case('processing') در حال پردازش @break
                                    @case('shipped') ارسال شده @break
                                    @case('completed') تحویل شده @break
                                    @case('cancelled') لغو شده @break
                                    @default {{ ucfirst($order->status) }}
                                @endswitch
                            </span>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                مشاهده
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            هیچ سفارشی یافت نشد.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6 dark:text-white">
        {{ $orders->links() }}
    </div>
@endsection