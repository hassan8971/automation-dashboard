<nav class="flex w-full justify-end p-4 z-50 relative">

    <div class="flex items-center gap-3 mr-4">

        <div class="relative group">
        
            <div id="nav-weather-widget" class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 border border-blue-100 dark:bg-blue-900/20 dark:border-blue-800 text-blue-600 dark:text-blue-300 cursor-help transition-colors">
                <span id="nav-weather-icon" class="text-lg">⏳</span>
                <span id="nav-weather-temp" class="text-sm font-bold ltr">--°</span>
            </div>

            <div class="absolute top-full mt-2 left-1/2 -translate-x-1/2 px-3 py-1.5 bg-gray-900 dark:bg-black text-white text-xs font-medium rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform group-hover:translate-y-0 translate-y-1 z-50 whitespace-nowrap pointer-events-none">
                تبریز
                <div class="absolute -top-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-gray-900 dark:bg-black rotate-45"></div>
            </div>

        </div>

    <div class="relative" id="todo-dropdown-wrapper">
        
        <button onclick="toggleTodoDropdown()" class="relative p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors group">
            <svg class="w-6 h-6 text-gray-600 dark:text-gray-300 hover:text-blue-main" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
            
            <span id="todo-badge" class="absolute top-1 right-1 h-3 w-3 bg-red-500 rounded-full border-2 border-white dark:border-dark-bg hidden animate-pulse"></span>
        </button>

        <div id="todo-dropdown-panel" 
     class="absolute top-full left-0 mt-4 w-[380px] bg-white dark:bg-[#2f3349] rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 transform scale-95 opacity-0 invisible transition-all duration-200 origin-top-left z-50 flex flex-col h-[500px]"> <div class="bg-gradient-to-r bg-blue-main p-3 rounded-t-2xl flex justify-between items-center text-white shadow-md z-10">
                <h3 class="font-bold text-sm">لیست کارهای من</h3>
                <div class="flex gap-2">
                    <button onclick="fetchTodos()" class="hover:bg-white/20 p-1 rounded-full"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg></button>
                    <button onclick="toggleTodoDropdown()" class="hover:bg-white/20 p-1 rounded-full"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
            </div>

            <div class="flex border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-dark-bg">
                <button onclick="switchTab('active')" id="tab-btn-active" class="flex-1 py-2 text-xs font-bold text-blue-main border-b-2 border-blue-main">تسک‌ها</button>
                <button onclick="switchTab('done')" id="tab-btn-done" class="flex-1 py-2 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">انجام شده</button>
            </div>

            <div id="todo-input-container" class="p-3 bg-gray-50 dark:bg-dark-bg border-b border-gray-200 dark:border-gray-700">
                <form onsubmit="addTodo(event)" class="space-y-2">
                    <input type="text" id="todo-title" required class="w-full px-3 py-1.5 bg-white dark:bg-dark-paper border border-gray-300 dark:border-gray-600 rounded-lg text-xs focus:ring-1 focus:ring-emerald-500 dark:text-white" placeholder="عنوان کار...">
                    <textarea id="todo-desc" rows="1" class="w-full px-3 py-1.5 bg-white dark:bg-dark-paper border border-gray-300 dark:border-gray-600 rounded-lg text-xs focus:ring-1 focus:ring-emerald-500 dark:text-white resize-none" placeholder="توضیحات..."></textarea>
                    <div class="flex gap-2 relative">
                        <input type="text" id="persian-date-input" required class="flex-1 px-3 py-1.5 bg-white dark:bg-dark-paper border border-gray-300 dark:border-gray-600 rounded-lg text-xs dark:text-white" placeholder="زمان..." autocomplete="off">
                        <input type="hidden" id="real-date-input" name="due_date">
                        <button type="submit" class="bg-blue-main hover:bg-blue-main-dark text-white px-3 py-1.5 rounded-lg text-xs font-medium">افزودن</button>
                    </div>
                </form>
            </div>

            <div class="flex-1 overflow-y-auto p-2 bg-white dark:bg-dark-paper rounded-b-2xl">
                <div id="todo-list-active" class="space-y-2"></div>
                <div id="todo-list-done" class="space-y-2 hidden"></div>
            </div>
        </div>
    </div>
</div>

                @php
                    $admin = auth()->guard('admin')->user();
                    $displayName = ($admin->use_nickname && !empty($admin->nickname)) ? $admin->nickname : $admin->name;
                    $hour = now()->hour;
                    $greeting = '';
                    $icon = '';

                    if ($hour >= 5 && $hour < 12) {
                        $greeting = 'صبح بخیر';
                        $icon = '☀️'; // خورشید صبح
                    } elseif ($hour >= 12 && $hour < 17) {
                        $greeting = 'ظهر بخیر';
                        $icon = '🌤️'; // خورشید وسط روز
                    } elseif ($hour >= 17 && $hour < 20) {
                        $greeting = 'عصر بخیر';
                        $icon = '🌇'; // غروب
                    } else {
                        $greeting = 'شب بخیر';
                        $icon = '🌙'; // ماه
                    }
                @endphp

                <div class="hidden md:flex items-center gap-2 ml-4 px-4 py-1.5 rounded-full bg-gray-50 border border-gray-200 dark:bg-gray-800 dark:border-gray-700 transition-colors duration-300">
                    
                    <span class="text-lg animate-pulse">{{ $icon }}</span>
                    
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-200">
                        <span class="font-bold text-blue-600 dark:text-blue-400">
                            {{ $displayName }}
                        </span> 
                        عزیز، {{ $greeting }}
                    </p>
                </div>


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

                <div class="relative" id="profile-dropdown-wrapper">
                    <button onclick="toggleProfileDropdown()" class="mr-2 relative group block w-10 h-10 rounded-full overflow-hidden border-2 border-gray-200 dark:border-gray-600 hover:border-emerald-500 transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                        <img src="{{ $admin->profile_photo_path ? asset('storage/'.$admin->profile_photo_path) : 'https://ui-avatars.com/api/?name='.$admin->name.'&background=random' }}" 
                            alt="Profile" 
                            class="w-full h-full object-cover">
                    </button>

                    <div id="profile-dropdown-panel" class="absolute top-full left-0 mt-2 w-48 bg-white dark:bg-dark-paper rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 transform scale-95 opacity-0 invisible transition-all duration-200 origin-top-left z-50">
                        
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                            <p class="text-sm font-bold text-gray-800 dark:text-white truncate">{{ $displayName }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">مدیر سیستم</p>
                        </div>

                        <div class="py-1">
                            <a href="{{ route('admin.profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-dark-hover transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                ویرایش پروفایل
                            </a>

                            <form action="{{ route('admin.logout') }}" method="POST" class="w-full">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                    خروج
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
            </nav>

