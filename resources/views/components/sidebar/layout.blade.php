<aside id="main-sidebar" class="w-64 bg-sidebar dark:bg-dark-paper text-gray-200 dark:text-dark-text flex flex-col transition-all duration-300 relative">
    
    <div class="sidebar-header h-20 flex items-center justify-between px-6 transition-all duration-300">
        <a href="#" class="text-xl font-bold text-white sidebar-text whitespace-nowrap">آکامد</a>

        <div class="flex items-center gap-2">
            

            <button id="sidebar-pin-btn" class="p-2 rounded-full hover:bg-gray-700 text-gray-400 hover:text-white transition-colors focus:outline-none">
                <svg id="pin-icon" class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
        </div>
    </div>

    <ul class="flex-grow px-4 py-2 space-y-2 sidebar-menu overflow-y-auto overflow-x-hidden">
        {{ $slot }}
    </ul>

    <div class="p-4 border-t border-slate-700 dark:border-dark-border mt-auto">
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button type="submit" class="flex items-center space-x-3 space-x-reverse w-full text-right px-4 py-2 rounded-lg text-gray-300 sidebar-menu-item hover:text-white hover:bg-gray-700 dark:hover:bg-dark-hover transition-colors">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                <span class="sidebar-text">خروج</span>
            </button>
        </form>
    </div>
</aside>