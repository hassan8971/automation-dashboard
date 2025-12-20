@extends('admin.layouts.app')

@section('title', 'داشبورد ادمین')

@section('header_banner')
@php
        $admin = auth()->guard('admin')->user();
        
        // اگر ادمین بنر آپلود کرده بود، آن را نشان بده، وگرنه بنر پیش‌فرض
        $bannerUrl = $admin->dashboard_banner_path 
            ? asset('storage/' . $admin->dashboard_banner_path) 
            : 'https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=1500&h=400&auto=format&fit=crop';
            
        // نام برند یا نام ادمین روی بنر
        $brandName = 'آکـامُـد'; // یا می‌توانید از دیتابیس بگیرید
@endphp
<div class="relative w-full h-48 md:h-64 lg:h-80 overflow-hidden group mb-4">
    
    <img src="{{ $bannerUrl }}" 
         alt="Brand Banner" 
         class="absolute inset-0 w-full h-full object-contain transition-transform duration-700">

    <!-- <div class="absolute inset-0 bg-gradient-to-r from-gray-900/90 via-gray-900/50 to-transparent"></div>

    <div class="relative z-10 h-full flex flex-col justify-center px-6 md:px-12 lg:px-20 max-w-7xl mx-auto">
        
        <span class="inline-flex items-center gap-2 self-start bg-emerald-500/20 backdrop-blur-md border border-emerald-500/30 text-emerald-300 text-xs font-bold px-3 py-1 rounded-full mb-4">
            <span class="relative flex h-2 w-2">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            پنل مدیریت
        </span>

        <h1 class="text-3xl md:text-5xl font-black text-white drop-shadow-lg mb-2 tracking-tight">
            {{ $brandName }}
        </h1>

        <p class="text-gray-200 text-sm md:text-lg font-light max-w-lg leading-relaxed">
            به پنل مدیریت خوش آمدید. امروز روز خوبی برای رشد کسب و کار است.
        </p>

    </div>
    
    <div class="absolute bottom-0 left-0 w-full h-16 bg-gradient-to-t from-gray-100 dark:from-dark-bg to-transparent"></div> -->

</div>
@endsection

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="widgets">

    <div class="grid grid-cols-1 gap-6 mb-6">
        <!-- CHATBOX -->
        <div id="chat-widget" class="relative overflow-hidden rounded-2xl bg-white dark:bg-dark-paper shadow-lg border border-gray-200 dark:border-gray-700 flex flex-col h-[500px] transition-all duration-500 ease-in-out" dir="rtl">
            
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 p-3 flex justify-between items-center text-white shadow-md z-20 shrink-0 h-[60px]">
                <div class="flex items-center gap-2">
                    <div class="bg-white/20 p-1.5 rounded-full"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" /></svg></div>
                    <h3 class="font-bold text-base">چت عمومی</h3>
                </div>
                <button onclick="toggleChatWidget()" class="hover:bg-white/20 p-1 rounded-full transition-colors"><svg id="chat-collapse-icon" class="w-6 h-6 transform transition-transform duration-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg></button>
            </div>

            <div class="flex-1 flex overflow-hidden">
                
                <div class="w-1/3 border-l border-gray-200 dark:border-gray-700 flex flex-col bg-gray-50/50 dark:bg-dark-bg/50">
                    
                    <div class="p-3 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-dark-paper">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300">وضعیت من:</span>
                            <button onclick="toggleMyStatus()" id="my-status-btn" class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors duration-300 focus:outline-none bg-gray-300">
                                <span id="my-status-dot" class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform duration-300 translate-x-0.5"></span>
                            </button>
                        </div>
                        <p id="my-status-text" class="text-[10px] text-gray-500 mt-1 text-left">آفلاین</p>
                    </div>

                    <div class="flex-1 overflow-y-auto p-2 space-y-2">
                        <p class="text-xs font-semibold text-gray-400 mb-2 px-1">لیست همکاران</p>
                        <div id="admins-list" class="space-y-1">
                            </div>
                    </div>
                </div>

                <div class="w-2/3 flex flex-col bg-gray-50 dark:bg-[#25293c]">
                    <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-3">
                        <div class="text-center text-gray-400 text-sm mt-10">در حال بارگذاری...</div>
                    </div>

                    <div class="p-2 bg-white dark:bg-dark-paper border-t border-gray-200 dark:border-gray-700">
                        <form id="chat-form" class="flex gap-2" onsubmit="sendMessage(event)">
                            <input type="text" id="message-input" class="flex-1 px-3 py-2 bg-gray-100 dark:bg-dark-bg border-0 rounded-lg text-sm focus:ring-1 focus:ring-indigo-500 dark:text-white" placeholder="پیام..." autocomplete="off">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white p-2 rounded-lg"><svg class="w-5 h-5 -rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg></button>
                        </form>
                    </div>
                </div>

            </div>
        </div>

        <!-- CHATBOX -->
    </div>
</div>

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



<script>
    // تنظیمات
    const FETCH_URL = "{{ route('admin.chat.fetch') }}";
    const SEND_URL = "{{ route('admin.chat.send') }}";
    const CSRF_TOKEN = "{{ csrf_token() }}";
    
    let chatContainer = document.getElementById('chat-messages');
    let messageInput = document.getElementById('message-input');
    let lastMessageCount = 0;
    let isUserScrolling = false;
    let isChatScrolling = false;

    // تشخیص اسکرول کاربر (برای اینکه وقتی کاربر داره بالا رو میخونه، نپره پایین)
    chatContainer.addEventListener('scroll', () => {
        if (chatContainer.scrollTop + chatContainer.clientHeight < chatContainer.scrollHeight - 50) {
            isUserScrolling = true;
        } else {
            isUserScrolling = false;
        }
    });

    // دریافت پیام‌ها
    function fetchMessages() {
        fetch(FETCH_URL)
            .then(res => res.json())
            .then(data => {
                const messages = data.messages;
                const currentUserId = data.current_user_id;

                // اگر پیام جدیدی نیامده، رندر نکنیم (بهینه‌سازی)
                // مگر اینکه بار اول باشد
                if (messages.length === lastMessageCount && lastMessageCount !== 0) return;
                
                chatContainer.innerHTML = ''; // پاک کردن موقت (در پروژه واقعی بهتر است Append شود)
                lastMessageCount = messages.length;

                if (messages.length === 0) {
                    chatContainer.innerHTML = '<div class="text-center text-gray-400 text-xs mt-4">هنوز پیامی نیست. شروع کنید!</div>';
                    return;
                }

                messages.forEach(msg => {
                    const isMe = msg.admin_id === currentUserId;
                    const time = new Date(msg.created_at).toLocaleTimeString('fa-IR', {hour: '2-digit', minute:'2-digit'});
                    
                    const html = `
                        <div class="flex flex-col ${isMe ? 'items-start' : 'items-end'}">
                            <div class="flex items-end gap-2 max-w-[85%] ${isMe ? 'flex-row' : 'flex-row-reverse'}">
                                
                                <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold text-white shrink-0 ${isMe ? 'bg-purple-500' : 'bg-gray-400'}">
                                    ${msg.admin.name.charAt(0)}
                                </div>

                                <div class="px-3 py-2 rounded-2xl text-sm shadow-sm relative group 
                                    ${isMe ? 'bg-purple-100 text-purple-900 rounded-tr-none' : 'bg-white dark:bg-gray-700 dark:text-gray-100 rounded-tl-none border border-gray-100 dark:border-gray-600'}">
                                    ${msg.message}
                                    <span class="text-[10px] opacity-50 block text-left mt-1 dir-ltr leading-none">${time}</span>
                                </div>
                            </div>
                            <span class="text-[10px] text-gray-400 px-9 mt-0.5">${isMe ? 'شما' : msg.admin.name}</span>
                        </div>
                    `;
                    chatContainer.insertAdjacentHTML('beforeend', html);
                });

                // اسکرول به پایین فقط اگر کاربر در حال خواندن پیام‌های قدیمی نیست
                if (!isUserScrolling) {
                    chatContainer.scrollTop = chatContainer.scrollHeight;
                }
            })
            .catch(err => console.error('Chat Error:', err));
    }

    function fetchAdminsStatus() {
        fetch("{{ route('admin.chat.users') }}")
            .then(res => res.json())
            .then(data => {
                // آپدیت دکمه وضعیت من
                updateMyStatusUI(data.my_status);

                // آپدیت لیست همکاران
                const list = document.getElementById('admins-list');
                if(!list) return;
                list.innerHTML = '';

                if (data.admins.length === 0) {
                    list.innerHTML = '<span class="text-[10px] text-gray-400 text-center block">همکار دیگری نیست</span>';
                }

                data.admins.forEach(admin => {
                    const img = admin.profile_photo_path ? `/storage/${admin.profile_photo_path}` : `https://ui-avatars.com/api/?name=${admin.name}&background=random`;
                    
                    const html = `
                        <div class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-dark-hover transition-colors group">
                            <div class="relative">
                                <img src="${img}" class="w-8 h-8 rounded-full object-cover border border-gray-200 dark:border-gray-600">
                                <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full border-2 border-white dark:border-dark-bg ${admin.is_online ? 'bg-green-500' : 'bg-gray-400'}"></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-700 dark:text-gray-200 truncate">${admin.name}</p>
                                <p class="text-[9px] ${admin.is_online ? 'text-green-500' : 'text-gray-400'}">${admin.is_online ? 'آنلاین' : 'آفلاین'}</p>
                            </div>
                        </div>
                    `;
                    list.insertAdjacentHTML('beforeend', html);
                });
            });
    }

    function toggleMyStatus() {
        fetch("{{ route('admin.chat.status') }}", {
            method: 'POST',
            headers: {'Content-Type': 'application/json','Accept': 'application/json','X-CSRF-TOKEN': "{{ csrf_token() }}"}
        })
        .then(res => res.json())
        .then(data => {
            updateMyStatusUI(data.status);
        });
    }

    // UI Helper for My Status Button
   // UI Helper for My Status Button
    function updateMyStatusUI(isOnline) {
        const btn = document.getElementById('my-status-btn');
        const dot = document.getElementById('my-status-dot');
        const text = document.getElementById('my-status-text');
        
        if (isOnline) {
            // === ONLINE STATE (Green) ===
            btn.classList.remove('bg-gray-300');
            btn.classList.add('bg-green-500');
            
            // Move to the LEFT (Negative translation)
            // -translate-x-5 ensures it hits the left side of the w-9 container
            dot.classList.remove('translate-x-0.5'); 
            dot.classList.add('-translate-x-5'); 
            
            text.innerText = 'شما آنلاین هستید';
            text.classList.remove('text-gray-500');
            text.classList.add('text-green-600', 'font-bold');
        } else {
            // === OFFLINE STATE (Gray) ===
            btn.classList.add('bg-gray-300');
            btn.classList.remove('bg-green-500');
            
            // Move to the RIGHT (Reset to start)
            // translate-x-0.5 gives it a tiny breathing room from the right border
            dot.classList.add('translate-x-0.5'); 
            dot.classList.remove('-translate-x-5');
            
            text.innerText = 'شما آفلاین هستید';
            text.classList.remove('text-green-600', 'font-bold');
            text.classList.add('text-gray-500');
        }
    }

    // --- CHAT WIDGET TOGGLE LOGIC ---
    // --- CHAT WIDGET TOGGLE LOGIC ---
    function toggleChatWidget() {
        const widget = document.getElementById('chat-widget');
        const icon = document.getElementById('chat-collapse-icon');
        
        // 1. Check for the correct height class (500px, not 400px)
        const isExpanded = widget.classList.contains('h-[500px]');

        if (isExpanded) {
            // Collapse
            widget.classList.remove('h-[500px]');
            widget.classList.add('h-[60px]'); // Match header height exactly
            
            icon.classList.add('rotate-180');
        } else {
            // Expand
            widget.classList.add('h-[500px]');
            widget.classList.remove('h-[60px]');
            
            icon.classList.remove('rotate-180');
        }
    }
    
    // ارسال پیام
    function sendMessage(e) {
        e.preventDefault();
        const text = messageInput.value.trim();
        if (!text) return;

        // پاک کردن ورودی سریع برای حس بهتر
        messageInput.value = '';

        fetch(SEND_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify({ message: text })
        })
        .then(res => res.json())
        .then(() => {
            fetchMessages(); // رفرش فوری
            isUserScrolling = false; // فورس اسکرول به پایین
        });
    }

    // اجرا
    document.addEventListener("DOMContentLoaded", function() {
        // Init Chat
        fetchMessages();
        fetchAdminsStatus();
        
        // Polling (هر ۵ ثانیه پیام‌ها و وضعیت‌ها چک می‌شوند)
        setInterval(() => {
            fetchMessages();
            fetchAdminsStatus();
        }, 5000);
        const msgBox = document.getElementById('chat-messages');
        if(msgBox) {
            msgBox.addEventListener('scroll', () => {
                isChatScrolling = Math.abs(msgBox.scrollHeight - msgBox.scrollTop - msgBox.clientHeight) > 10;
            });
        }
    });
</script>


@endsection