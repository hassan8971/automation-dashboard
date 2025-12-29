<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    @include('admin.layouts.head')
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
                <x-sidebar.sub-link title="همه دسته بندی ها" href="{{ route('admin.categories.index') }}" :active="request()->routeIs('admin.categories.index')"/>
                <x-sidebar.sub-link title="افزودن دسته بندی" href="{{ route('admin.categories.create') }}" :active="request()->routeIs('admin.categories.create')"/>
            </x-sidebar.group>

            <x-sidebar.group title="محصولات" :active="request()->routeIs('admin.products.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </x-slot:icon>
                <x-sidebar.sub-link title="همه محصولات" href="{{ route('admin.products.index') }}" :active="request()->routeIs('admin.products.index')"/>
                <x-sidebar.sub-link title="افزودن محصول" href="{{ route('admin.products.create') }}" :active="request()->routeIs('admin.products.create')"/>
            </x-sidebar.group>

            <x-sidebar.group title="اشتراک ها" :active="request()->routeIs('admin.subscriptions.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </x-slot:icon>
                <x-sidebar.sub-link title="همه اشتراک ها" href="{{ route('admin.subscriptions.index') }}" :active="request()->routeIs('admin.subscriptions.index')"/>
                <x-sidebar.sub-link title="افزودن اشتراک" href="{{ route('admin.subscriptions.create') }}" :active="request()->routeIs('admin.subscriptions.create')"/>
            </x-sidebar.group>

            <x-sidebar.group title="گیفت ها" :active="request()->routeIs('admin.gifts.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </x-slot:icon>
                <x-sidebar.sub-link title="همه گیفت ها" href="{{ route('admin.gifts.index') }}" :active="request()->routeIs('admin.gifts.index')"/>
                <x-sidebar.sub-link title="افزودن گیفت" href="{{ route('admin.gifts.create') }}" :active="request()->routeIs('admin.gifts.create')"/>
            </x-sidebar.group>

            <x-sidebar.group title="افزونه ها" :active="request()->routeIs('admin.addons.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </x-slot:icon>
                <x-sidebar.sub-link title="همه افزونه ها" href="{{ route('admin.addons.index') }}" :active="request()->routeIs('admin.addons.index')"/>
                <x-sidebar.sub-link title="افزودن افزونه" href="{{ route('admin.addons.create') }}" :active="request()->routeIs('admin.addons.create')"/>
            </x-sidebar.group>

            <x-sidebar.group title="ریدیم کد ها" :active="request()->routeIs('admin.redeem-codes.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </x-slot:icon>
                <x-sidebar.sub-link title="همه ریدیم کد ها" href="{{ route('admin.redeem-codes.index') }}" :active="request()->routeIs('admin.redeem-codes.index')"/>
                <x-sidebar.sub-link title="افزودن ریدیم کد" href="{{ route('admin.redeem-codes.create') }}" :active="request()->routeIs('admin.redeem-codes.create')"/>
            </x-sidebar.group>

            <x-sidebar.link title="کیف پول ها" href="{{ route('admin.wallets.index') }}" :active="request()->routeIs('admin.wallets.*')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </x-sidebar.link>

            <x-sidebar.group title="انواع بسته بندی" :active="request()->routeIs('admin.packaging-options.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </x-slot:icon>
                <x-sidebar.sub-link title="همه بسته بندی ها" href="{{ route('admin.packaging-options.index') }}" :active="request()->routeIs('admin.packaging-options.index')"/>
                <x-sidebar.sub-link title="افزودن بسته بندی" href="{{ route('admin.packaging-options.create') }}" :active="request()->routeIs('admin.packaging-options.create')"/>
            </x-sidebar.group>

            <x-sidebar.group title="کد های تخفیف" :active="request()->routeIs('admin.discounts.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                </x-slot:icon>
                <x-sidebar.sub-link title="همه کد ها" href="{{ route('admin.discounts.index') }}" :active="request()->routeIs('admin.discounts.index')"/>
                <x-sidebar.sub-link title="افزودن کد تخفیف" href="{{ route('admin.discounts.create') }}" :active="request()->routeIs('admin.discounts.create')"/>
            </x-sidebar.group>

            <div class="px-4 py-2">
                <span class="text-xs font-semibold text-menu-title uppercase text-gray-500">ویژگی‌ها</span>
            </div>

            <x-sidebar.group title="کتابخانه ویدیو" :active="request()->routeIs('admin.videos.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M4 5h11a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V6a1 1 0 011-1z"></path></svg>
                </x-slot:icon>
                <x-sidebar.sub-link title="همه ویدیو ها" href="{{ route('admin.videos.index') }}" :active="request()->routeIs('admin.videos.index')"/>
                <x-sidebar.sub-link title="افزودن ویدیو" href="{{ route('admin.videos.create') }}" :active="request()->routeIs('admin.videos.create')"/>
            </x-sidebar.group>

            <x-sidebar.group title="مدیریت سایز ها" :active="request()->routeIs('admin.sizes.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 1v4m0 0h-4m4 0l-5-5"></path></svg>
                </x-slot:icon>
                <x-sidebar.sub-link title="لیست سایز ها" href="{{ route('admin.sizes.index') }}" :active="request()->routeIs('admin.sizes.index')"/>
                <x-sidebar.sub-link title="افزودن سایز" href="{{ route('admin.sizes.create') }}" :active="request()->routeIs('admin.sizes.create')"/>
            </x-sidebar.group>

            <x-sidebar.group title="مدیریت رنگ ها" :active="request()->routeIs('admin.colors.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                </x-slot:icon>
                <x-sidebar.sub-link title="لیست رنگ ها" href="{{ route('admin.colors.index') }}" :active="request()->routeIs('admin.colors.index')"/>
                <x-sidebar.sub-link title="افزودن رنگ" href="{{ route('admin.colors.create') }}" :active="request()->routeIs('admin.colors.create')"/>
            </x-sidebar.group>

            <x-sidebar.group title="منابع خرید" :active="request()->routeIs('admin.buy-sources.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0v-4a1 1 0 011-1h2a1 1 0 011 1v4m-4 0V9m0 0h14m-14 0V5m14 16v-4a1 1 0 00-1-1h-2a1 1 0 00-1 1v4m-4 0V9"></path></svg>
                </x-slot:icon>
                <x-sidebar.sub-link title="لیست منابع" href="{{ route('admin.buy-sources.index') }}" :active="request()->routeIs('admin.buy-sources.index')"/>
                <x-sidebar.sub-link title="افروزدن" href="{{ route('admin.buy-sources.create') }}" :active="request()->routeIs('admin.buy-sources.create')"/>
            </x-sidebar.group>

            <x-sidebar.group title="مدیریت منو" :active="request()->routeIs('admin.menu-items.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </x-slot:icon>
                <x-sidebar.sub-link title="منو ها" href="{{ route('admin.menu-items.index') }}" :active="request()->routeIs('admin.menu-items.index')"/>
                <x-sidebar.sub-link title="افروزدن" href="{{ route('admin.menu-items.create') }}" :active="request()->routeIs('admin.menu-items.create')"/>
            </x-sidebar.group>

        </x-sidebar.layout>

        <main class="flex-grow">
            
            @include('admin.layouts.header')
            @yield('header_banner')

            <div class="max-w-7xl mx-auto p-8">
                @yield('content')
            </div>
        </main>
        
    </div>

    <div id="global-toast-container" class="fixed bottom-6 left-6 z-50 flex flex-col gap-4"></div>

    @include('admin.layouts.scripts')
    @stack('scripts')
</body>
</html>