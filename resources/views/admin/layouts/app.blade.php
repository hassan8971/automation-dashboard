<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <title>@yield('title', 'پنل مدیریت') - آکامد</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: {
                            bg: '#25293c',
                            paper: '#2f3349',
                            text: '#cfcde4',
                            hover: '#3a3b64',
                            border: '#4b4b4b'
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: IRANSansX, sans-serif; }
        .fade-enter { opacity: 0; transform: scale(0.95); pointer-events: none; }
        .fade-enter-active { opacity: 1; transform: scale(1); pointer-events: auto; transition: all 0.1s ease-out; }
        
        /* Anti-flicker styles */
        .force-collapse aside#main-sidebar { width: 5rem !important; }
        .force-collapse .sidebar-text, 
        .force-collapse .sidebar-arrow, 
        .force-collapse .text-menu-title { display: none !important; }
    </style>
    <link href="{{ asset('css/all.css') }}" rel="stylesheet">
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">

    <script>
        // 1. Check Sidebar State
        if (localStorage.getItem('sidebar-pinned') === 'false') {
            document.documentElement.classList.add('force-collapse');
        }

        // 2. Check Theme State
        const savedTheme = localStorage.getItem('theme');
        const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches;
        if (savedTheme === 'dark' || (!savedTheme && systemTheme)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-gray-100 text-gray-800 dark:bg-dark-bg dark:text-dark-text transition-colors duration-300">

    <div class="flex min-h-screen flex-row">
        
        <x-sidebar.layout>

            <x-sidebar.link title="داشبورد" href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">
                <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='24' height='24'><g fill='none' stroke='black' stroke-linecap='round' stroke-linejoin='round' stroke-width='2'><path d='m19 8.71l-5.333-4.148a2.666 2.666 0 0 0-3.274 0L5.059 8.71a2.67 2.67 0 0 0-1.029 2.105v7.2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-7.2c0-.823-.38-1.6-1.03-2.105'/><path d='M16 15c-2.21 1.333-5.792 1.333-8 0'/></g></svg>

            </x-sidebar.link>

            <x-sidebar.link title="سفارشات" href="{{ route('admin.orders.index') }}" :active="request()->routeIs('admin.orders.*')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </x-sidebar.link>

            <x-sidebar.link title="مشتریان" href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.*')">
                                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </x-sidebar.link>

            <x-sidebar.group title="دسته بندی ها" :active="request()->routeIs('admin.categories.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>

                </x-slot:icon>

                <x-sidebar.sub-link title="همه دسته بندی ها" href="{{ route('admin.categories.index') }}" :active="request()->routeIs('admin.categories.index')" :active="request()->routeIs('admin.categories.index')"/>
                <x-sidebar.sub-link title="افزودن دسته بندی" href="{{ route('admin.categories.create') }}" :active="request()->routeIs('admin.categories.create')" :active="request()->routeIs('admin.categories.create')"/>
            </x-sidebar.group>

            <x-sidebar.group title="محصولات" :active="request()->routeIs('admin.products.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </x-slot:icon>

                <x-sidebar.sub-link title="همه محصولات" href="{{ route('admin.products.index') }}" :active="request()->routeIs('admin.products.index')" :active="request()->routeIs('admin.products.index')"/>
                <x-sidebar.sub-link title="افزودن محصول" href="{{ route('admin.products.create') }}" :active="request()->routeIs('admin.products.create')" :active="request()->routeIs('admin.products.create')"/>
            </x-sidebar.group>

            <x-sidebar.group title="انواع بسته بندی" :active="request()->routeIs('admin.packaging-options.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </x-slot:icon>

                <x-sidebar.sub-link title="همه بسته بندی ها" href="{{ route('admin.packaging-options.index') }}" :active="request()->routeIs('admin.packaging-options.index')" :active="request()->routeIs('admin.packaging-options.index')"/>
                <x-sidebar.sub-link title="افزودن بسته بندی" href="{{ route('admin.packaging-options.create') }}" :active="request()->routeIs('admin.packaging-options.create')" :active="request()->routeIs('admin.packaging-options.create')"/>
            </x-sidebar.group>

            <x-sidebar.group title="کد های تخفیف" :active="request()->routeIs('admin.discounts.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                </x-slot:icon>

                <x-sidebar.sub-link title="همه کد ها" href="{{ route('admin.discounts.index') }}" :active="request()->routeIs('admin.discounts.index')" :active="request()->routeIs('admin.discounts.index')"/>
                <x-sidebar.sub-link title="افزودن کد تخفیف" href="{{ route('admin.discounts.create') }}" :active="request()->routeIs('admin.discounts.create')" :active="request()->routeIs('admin.discounts.create')"/>
            </x-sidebar.group>

            <div class="px-4 py-2">
                <span class="text-xs font-semibold text-menu-title uppercase text-gray-500">ویژگی‌ها</span>
            </div>

            <x-sidebar.group title="کتابخانه ویدیو" :active="request()->routeIs('admin.videos.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M4 5h11a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V6a1 1 0 011-1z"></path></svg>
                </x-slot:icon>

                <x-sidebar.sub-link title="همه ویدیو ها" href="{{ route('admin.videos.index') }}" :active="request()->routeIs('admin.videos.index')" :active="request()->routeIs('admin.videos.index')"/>
                <x-sidebar.sub-link title="افزودن ویدیو" href="{{ route('admin.videos.create') }}" :active="request()->routeIs('admin.videos.create')" :active="request()->routeIs('admin.videos.create')"/>
            </x-sidebar.group>

            <x-sidebar.group title="مدیریت سایز ها" :active="request()->routeIs('admin.sizes.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 1v4m0 0h-4m4 0l-5-5"></path></svg>
                </x-slot:icon>

                <x-sidebar.sub-link title="لیست سایز ها" href="{{ route('admin.sizes.index') }}" :active="request()->routeIs('admin.sizes.index')" :active="request()->routeIs('admin.sizes.index')"/>
                <x-sidebar.sub-link title="افزودن سایز" href="{{ route('admin.sizes.create') }}" :active="request()->routeIs('admin.sizes.create')" :active="request()->routeIs('admin.sizes.create')"/>
            </x-sidebar.group>

            <x-sidebar.group title="مدیریت رنگ ها" :active="request()->routeIs('admin.colors.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                </x-slot:icon>

                <x-sidebar.sub-link title="لیست رنگ ها" href="{{ route('admin.colors.index') }}" :active="request()->routeIs('admin.colors.index')" :active="request()->routeIs('admin.colors.index')"/>
                <x-sidebar.sub-link title="افزودن رنگ" href="{{ route('admin.colors.create') }}" :active="request()->routeIs('admin.colors.create')" :active="request()->routeIs('admin.colors.create')"/>
            </x-sidebar.group>

            <x-sidebar.group title="منابع خرید" :active="request()->routeIs('admin.buy-sources.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0v-4a1 1 0 011-1h2a1 1 0 011 1v4m-4 0V9m0 0h14m-14 0V5m14 16v-4a1 1 0 00-1-1h-2a1 1 0 00-1 1v4m-4 0V9"></path></svg>
                </x-slot:icon>

                <x-sidebar.sub-link title="لیست منابع" href="{{ route('admin.buy-sources.index') }}" :active="request()->routeIs('admin.buy-sources.index')" :active="request()->routeIs('admin.buy-sources.index')"/>
                <x-sidebar.sub-link title="افروزدن" href="{{ route('admin.buy-sources.create') }}" :active="request()->routeIs('admin.buy-sources.create')" :active="request()->routeIs('admin.buy-sources.create')"/>
            </x-sidebar.group>

            <x-sidebar.group title="مدیریت منو" :active="request()->routeIs('admin.menu-items.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </x-slot:icon>

                <x-sidebar.sub-link title="منو ها" href="{{ route('admin.menu-items.index') }}" :active="request()->routeIs('admin.menu-items.index')" :active="request()->routeIs('admin.menu-items.index')"/>
                <x-sidebar.sub-link title="افروزدن" href="{{ route('admin.menu-items.create') }}" :active="request()->routeIs('admin.menu-items.create')" :active="request()->routeIs('admin.menu-items.create')"/>
            </x-sidebar.group>

        </x-sidebar.layout>

        <main class="flex-grow p-8">
            <nav class="flex w-full justify-end mb-6 z-50 relative">
                <div class="relative">
                    <button id="theme-toggle-btn" class="p-2 rounded-full bg-white dark:bg-dark-paper shadow-sm hover:bg-gray-100 dark:hover:bg-dark-hover text-gray-500 dark:text-gray-200 transition-colors focus:outline-none border border-gray-200 dark:border-dark-border">
                        <svg id="theme-icon-sun" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <svg id="theme-icon-moon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    </button>

                    <div id="theme-dropdown" class="absolute left-0 mt-2 w-32 bg-white dark:bg-dark-paper rounded-lg shadow-lg border border-gray-200 dark:border-dark-border py-1 fade-enter z-50">
                        <button onclick="setTheme('light')" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-dark-hover">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            روشن
                        </button>
                        <button onclick="setTheme('dark')" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-dark-hover">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                            تاریک
                        </button>
                        <button onclick="setTheme('system')" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-dark-hover">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            سیستم
                        </button>
                    </div>
                </div>
            </nav>

            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>
        
    </div>

    <script>
        // --- GLOBAL FUNCTIONS ---
        function setTheme(mode) {
            if (mode === 'system') {
                localStorage.removeItem('theme');
                if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    document.documentElement.classList.add('dark');
                    updateIcon('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    updateIcon('light');
                }
            } else {
                localStorage.setItem('theme', mode);
                if (mode === 'dark') {
                    document.documentElement.classList.add('dark');
                    updateIcon('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    updateIcon('light');
                }
            }
        }

        function updateIcon(mode) {
            const sunIcon = document.getElementById('theme-icon-sun');
            const moonIcon = document.getElementById('theme-icon-moon');
            if (!sunIcon || !moonIcon) return;

            if (mode === 'dark') {
                sunIcon.classList.add('hidden');
                moonIcon.classList.remove('hidden');
            } else {
                sunIcon.classList.remove('hidden');
                moonIcon.classList.add('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Icon
            if (document.documentElement.classList.contains('dark')) {
                updateIcon('dark');
            } else {
                updateIcon('light');
            }

            // Theme Dropdown Logic
            const themeBtn = document.getElementById('theme-toggle-btn');
            const themeDropdown = document.getElementById('theme-dropdown');

            if(themeBtn && themeDropdown) {
                themeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    themeDropdown.classList.toggle('fade-enter');
                    themeDropdown.classList.toggle('fade-enter-active');
                });
                document.addEventListener('click', () => {
                    themeDropdown.classList.add('fade-enter');
                    themeDropdown.classList.remove('fade-enter-active');
                });
            }

            // --- SIDEBAR LOGIC ---
            const sidebar = document.getElementById('main-sidebar');
            const pinBtn = document.getElementById('sidebar-pin-btn');
            const pinIcon = document.getElementById('pin-icon');
            const toggles = document.querySelectorAll('.sidebar-toggle');
            const submenus = document.querySelectorAll('.sidebar-submenu');

            // Saved State
            const savedState = localStorage.getItem('sidebar-pinned');
            let isPinned = savedState === null ? true : (savedState === 'true');

            if (!isPinned) {
                sidebar.classList.add('collapsed');
                pinIcon.classList.add('rotate-180');
            } else {
                sidebar.classList.remove('collapsed');
                pinIcon.classList.remove('rotate-180');
            }

            document.documentElement.classList.remove('force-collapse');

            // Pin Logic
            if(pinBtn) {
                pinBtn.addEventListener('click', () => {
                    isPinned = !isPinned;
                    localStorage.setItem('sidebar-pinned', isPinned);
                    if (isPinned) {
                        sidebar.classList.remove('collapsed');
                        pinIcon.classList.remove('rotate-180');
                    } else {
                        sidebar.classList.add('collapsed');
                        pinIcon.classList.add('rotate-180');
                    }
                });
            }

            // Hover Logic
            if(sidebar) {
                sidebar.addEventListener('mouseenter', () => { if (!isPinned) sidebar.classList.remove('collapsed'); });
                sidebar.addEventListener('mouseleave', () => { if (!isPinned) sidebar.classList.add('collapsed'); });
            }

            // Submenus
            submenus.forEach(submenu => {
                if (!submenu.classList.contains('max-h-0')) submenu.style.maxHeight = submenu.scrollHeight + "px";
            });

            toggles.forEach(toggle => {
                toggle.addEventListener('click', function() {
                    const arrow = this.querySelector('.sidebar-arrow');
                    const submenu = this.nextElementSibling;
                    if (!submenu || !arrow) return;

                    if (submenu.style.maxHeight) {
                        submenu.style.maxHeight = null; 
                        submenu.classList.add('max-h-0');
                        arrow.classList.remove('rotate-90');
                        arrow.classList.add('rotate-180');
                    } else {
                        submenu.style.maxHeight = submenu.scrollHeight + "px";
                        arrow.classList.remove('rotate-180');
                        arrow.classList.add('rotate-90');
                    }
                });
            });
        });
    </script>
</body>
</html>