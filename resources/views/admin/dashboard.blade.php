@extends('admin.layouts.app')

@section('title', 'داشبورد ادمین')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div dir="rtl">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 dark:text-white">داشبورد</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <a href="{{ route('admin.orders.index') }}" 
           class="bg-white dark:bg-dark-paper shadow-md rounded-lg p-6 flex items-center justify-between 
                  transition-all duration-300 ease-in-out hover:shadow-xl hover:-translate-y-1 dark:hover:bg-dark-hover group">
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">درآمد کل</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">
                    {{ number_format($totalRevenue) }} <span class="text-lg font-normal text-gray-600 dark:text-gray-400">تومان</span>
                </p>
            </div>
            <span class="p-3 bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-400 rounded-full">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2-1.343-2-3-2zM0 14s3-2 6-2 6 2 6 2v2H0v-2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2-1.343-2-3-2zm0 0V6m0 8v2m0-6a2 2 0 100 4 2 2 0 000-4z"></path></svg>
            </span>
        </a>
        
        <a href="{{ route('admin.orders.index') }}" 
           class="bg-white dark:bg-dark-paper shadow-md rounded-lg p-6 flex items-center justify-between
                  transition-all duration-300 ease-in-out hover:shadow-xl hover:-translate-y-1 dark:hover:bg-dark-hover group">
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">کل سفارشات</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">
                    {{ number_format($totalOrders) }} <span class="text-lg font-normal text-gray-600 dark:text-gray-400">عدد</span>
                </p>
            </div>
            <span class="p-3 bg-green-100 text-green-600 dark:bg-green-900/50 dark:text-green-400 rounded-full">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </span>
        </a>
        
        <a href="{{ route('admin.users.index') }}" 
           class="bg-white dark:bg-dark-paper shadow-md rounded-lg p-6 flex items-center justify-between
                  transition-all duration-300 ease-in-out hover:shadow-xl hover:-translate-y-1 dark:hover:bg-dark-hover group">
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">کل مشتریان</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">
                    {{ number_format($totalCustomers) }} <span class="text-lg font-normal text-gray-600 dark:text-gray-400">کاربر</span>
                </p>
            </div>
            <span class="p-3 bg-yellow-100 text-yellow-600 dark:bg-yellow-900/50 dark:text-yellow-400 rounded-full">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </span>
        </a>
        
        <a href="{{ route('admin.products.index') }}" 
           class="bg-white dark:bg-dark-paper shadow-md rounded-lg p-6 flex items-center justify-between
                  transition-all duration-300 ease-in-out hover:shadow-xl hover:-translate-y-1 dark:hover:bg-dark-hover group">
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">تعداد محصولات</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">
                    {{ number_format($totalProducts) }} <span class="text-lg font-normal text-gray-600 dark:text-gray-400">کالا</span>
                </p>
            </div>
            <span class="p-3 bg-red-100 text-red-600 dark:bg-red-900/50 dark:text-red-400 rounded-full">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 bg-white dark:bg-dark-paper shadow-md rounded-lg p-6">
            <h3 class="text-xl font-semibold mb-4 dark:text-white">درآمد ۷ روز گذشته</h3>
            <div class="relative h-96">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
        <div class="bg-white dark:bg-dark-paper shadow-md rounded-lg p-6">
            <h3 class="text-xl font-semibold mb-4 dark:text-white">فروش بر اساس دسته‌بندی</h3>
            <div class="relative h-96">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-dark-paper shadow-md rounded-lg overflow-hidden">
        <h3 class="text-xl font-semibold p-6 dark:text-white">۵ سفارش اخیر</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-dark-border bg-gray-100 dark:bg-dark-hover text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">شماره سفارش</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-dark-border bg-gray-100 dark:bg-dark-hover text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">مشتری</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-dark-border bg-gray-100 dark:bg-dark-hover text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">مبلغ کل</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-dark-border bg-gray-100 dark:bg-dark-hover text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">وضعیت</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 dark:text-dark-text">
                    @forelse ($recentOrders as $order)
                        <tr class="hover:bg-gray-50 dark:hover:bg-dark-hover transition-colors">
                            <td class="px-5 py-4 border-b border-gray-200 dark:border-dark-border">
                                <a href="{{ route('admin.orders.show', $order) }}" class="font-medium text-blue-600 dark:text-blue-400 hover:underline">
                                    {{ $order->order_code ?? "#".$order->id }}
                                </a>
                            </td>
                            <td class="px-5 py-4 border-b border-gray-200 dark:border-dark-border">
                                {{ $order->user->name ?? 'کاربر مهمان' }}
                            </td>
                            <td class="px-5 py-4 border-b border-gray-200 dark:border-dark-border">
                                {{ number_format($order->total) }} تومان
                            </td>
                            <td class="px-5 py-4 border-b border-gray-200 dark:border-dark-border">
                                <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-dark-border">
                                هنوز سفارشی ثبت نشده است.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        // Define colors that work well on both Light and Dark backgrounds for Charts
        const textColor = '#9ca3af'; // Gray 400
        const gridColor = 'rgba(156, 163, 175, 0.2)'; // Faint gray

        // --- نمودار درآمد (Revenue Chart) ---
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: @json($chartLabels), // لیبل‌های شمسی از کنترلر
                datasets: [{
                    label: 'درآمد (تومان)',
                    data: @json($chartData), // داده‌های درآمد از کنترلر
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        ticks: { color: textColor },
                        grid: { color: gridColor }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: textColor,
                            callback: function(value) {
                                // فرمت کردن اعداد به تومان
                                return new Intl.NumberFormat('fa-IR').format(value) + ' ت';
                            }
                        },
                        grid: { color: gridColor }
                    }
                },
                plugins: {
                    legend: {
                        labels: { color: textColor }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('fa-IR').format(context.parsed.y) + ' تومان';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });

        // --- نمودار دسته‌بندی (Category Chart) ---
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        const categoryChart = new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: @json($categoryChartLabels), // داده‌های ساختگی از کنترلر
                datasets: [{
                    label: 'فروش بر اساس دسته‌بندی',
                    data: @json($categoryChartData),
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(245, 158, 11, 0.7)',
                    ],
                    borderColor: [
                        'rgba(59, 130, 246, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(245, 158, 11, 1)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: textColor, // Make legend visible in dark mode
                            font: {
                                family: 'Vazirmatn' // (اگر فونت وزیر را دارید)
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection