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
    <link rel="stylesheet" href="https://unpkg.com/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css">
    
    <style>
        /* انیمیشن ورود و لرزش ملایم برای هشدار */
        @keyframes slideInUp {
            from { transform: translateY(100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes gentleBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        .toast-enter { animation: slideInUp 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
        .toast-idle { animation: gentleBounce 2s infinite ease-in-out; }
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/persian-date@1.1.0/dist/persian-date.min.js"></script>
    <script src="https://unpkg.com/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js"></script>

    <script>
    // --- CONFIG ---
    const TODO_URL = "{{ route('admin.todos.index') }}";
    const CSRF_TOKEN_TODO = "{{ csrf_token() }}";
    
    // --- 1. GREETING & WEATHER LOGIC ---
    document.addEventListener("DOMContentLoaded", () => {
        
        // Fetch Weather (Mini Version)
        fetch('https://api.open-meteo.com/v1/forecast?latitude=38.08&longitude=46.29&current_weather=true')
            .then(res => res.json())
            .then(data => {
                const temp = Math.round(data.current_weather.temperature);
                const code = data.current_weather.weathercode;
                
                // --- FIX STARTS HERE ---
                // We must define the hour here to check for day/night
                const hour = new Date().getHours(); 
                const isDay = hour > 6 && hour < 19;
                // --- FIX ENDS HERE ---

                document.getElementById('nav-weather-temp').innerText = temp + '°C';
                
                // Simple Icon mapping
                let wIcon = '🌡️';
                
                if (code === 0) wIcon = isDay ? '☀️' : '🌙';
                else if (code <= 3) wIcon = isDay ? '⛅' : '☁️';
                else if (code >= 45 && code <= 48) wIcon = '🌫️';
                else if (code >= 51) wIcon = '🌧️';
                else if (code >= 71) wIcon = '❄️'; // Added Snow
                else if (code >= 95) wIcon = '⛈️'; // Added Thunder
                
                document.getElementById('nav-weather-icon').innerText = wIcon;
            })
            .catch((err) => {
                console.error('Weather Error:', err); // Added log to see errors in console
                document.getElementById('nav-weather-temp').innerText = 'N/A';
            });
            
        // Init Todos
        if(typeof fetchTodos === 'function') {
            fetchTodos();
        }
    });

    // --- 2. DROPDOWN TOGGLE LOGIC ---
    function toggleTodoDropdown() {
        const panel = document.getElementById('todo-dropdown-panel');
        if (!panel) return;
        
        const isClosed = panel.classList.contains('invisible');

        if (isClosed) {
            // Open
            panel.classList.remove('invisible', 'opacity-0', 'scale-95');
            panel.classList.add('visible', 'opacity-100', 'scale-100');
        } else {
            // Close
            panel.classList.add('invisible', 'opacity-0', 'scale-95');
            panel.classList.remove('visible', 'opacity-100', 'scale-100');
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const wrapper = document.getElementById('todo-dropdown-wrapper');
        const panel = document.getElementById('todo-dropdown-panel');
        
        // Check if the click happened inside the Datepicker popup
        // The library usually uses the class 'datepicker-plot-area'
        const isDatepicker = event.target.closest('.datepicker-plot-area') || 
                             event.target.closest('.datepicker-container');

        // Close ONLY if:
        // 1. Click is outside the Todo Wrapper
        // 2. AND Click is NOT inside the Datepicker
        if (wrapper && !wrapper.contains(event.target) && !isDatepicker && panel) {
             panel.classList.add('invisible', 'opacity-0', 'scale-95');
             panel.classList.remove('visible', 'opacity-100', 'scale-100');
        }
    });

    // --- 3. TODO WIDGET LOGIC ---
    // Init Datepicker
    $(document).ready(function() {
        if($('#persian-date-input').length > 0) {
            $('#persian-date-input').pDatepicker({
                initialValue: false,
                autoClose: true,
                timePicker: { enabled: true, meridian: { enabled: false } },
                format: 'YYYY/MM/DD HH:mm',
                onSelect: function(unixDate) {
                    const date = new Date(unixDate);
                    // Format to MySQL
                    const d = new Date(date.getTime() - (date.getTimezoneOffset() * 60000)).toISOString().slice(0, 19).replace('T', ' ');
                    $('#real-date-input').val(d);
                }
            });
        }
    });

    function switchTab(tab) {
        const activeBtn = document.getElementById('tab-btn-active');
        const doneBtn = document.getElementById('tab-btn-done');
        const activeList = document.getElementById('todo-list-active');
        const doneList = document.getElementById('todo-list-done');
        const inputContainer = document.getElementById('todo-input-container');

        if (tab === 'active') {
            activeBtn.classList.add('border-emerald-600', 'text-emerald-600', 'font-bold');
            activeBtn.classList.remove('border-transparent', 'text-gray-500', 'font-medium');
            
            doneBtn.classList.remove('border-emerald-600', 'text-emerald-600', 'font-bold');
            doneBtn.classList.add('border-transparent', 'text-gray-500', 'font-medium');
            
            activeList.classList.remove('hidden');
            doneList.classList.add('hidden');
            inputContainer.classList.remove('hidden');
        } else {
            doneBtn.classList.add('border-emerald-600', 'text-emerald-600', 'font-bold');
            doneBtn.classList.remove('border-transparent', 'text-gray-500', 'font-medium');
            
            activeBtn.classList.remove('border-emerald-600', 'text-emerald-600', 'font-bold');
            activeBtn.classList.add('border-transparent', 'text-gray-500', 'font-medium');
            
            doneList.classList.remove('hidden');
            activeList.classList.add('hidden');
            inputContainer.classList.add('hidden');
        }
    }

    function fetchTodos() {
        fetch(TODO_URL)
            .then(res => res.json())
            .then(todos => {
                const activeList = document.getElementById('todo-list-active');
                const doneList = document.getElementById('todo-list-done');
                const badge = document.getElementById('todo-badge');
                
                if(!activeList || !doneList) return;

                activeList.innerHTML = '';
                doneList.innerHTML = '';

                const activeTodos = todos.filter(t => !t.is_completed);
                const doneTodos = todos.filter(t => t.is_completed);
                let urgentCount = 0;

                // Check urgent tasks for Badge
                activeTodos.forEach(todo => {
                    const hoursLeft = (new Date(todo.due_date) - new Date()) / 36e5;
                    if (hoursLeft < 2) urgentCount++;
                    activeList.insertAdjacentHTML('beforeend', createTodoHTML(todo));
                });

                doneTodos.forEach(todo => {
                    doneList.insertAdjacentHTML('beforeend', createTodoHTML(todo));
                });

                // Toggle Badge
                if (urgentCount > 0 && badge) badge.classList.remove('hidden');
                else if(badge) badge.classList.add('hidden');

                if (activeTodos.length === 0) activeList.innerHTML = '<div class="text-center text-gray-400 text-xs mt-8">همه کارها انجام شده!</div>';
                if (doneTodos.length === 0) doneList.innerHTML = '<div class="text-center text-gray-400 text-xs mt-8">خالی</div>';
            });
    }

    function createTodoHTML(todo) {
        const dateStr = new Date(todo.due_date).toLocaleString('fa-IR', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
        const isUrgent = !todo.is_completed && ((new Date(todo.due_date) - new Date()) / 36e5 < 2);

        return `
            <div onclick="toggleTaskExpand(${todo.id})" id="task-card-${todo.id}"
                 class="group cursor-pointer flex flex-col p-2.5 rounded-lg border transition-all hover:bg-gray-50 dark:hover:bg-gray-700
                ${isUrgent ? 'border-red-200 bg-red-50 dark:bg-red-900/10' : 'border-gray-100 dark:border-gray-700'}">
                <div class="flex items-start gap-2 overflow-hidden w-full">
                    <button onclick="event.stopPropagation(); toggleTodo(${todo.id})" 
                            class="mt-0.5 w-4 h-4 shrink-0 rounded border flex items-center justify-center transition-colors
                            ${todo.is_completed ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-gray-400 hover:border-emerald-500'}">
                        ${todo.is_completed ? '<svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>' : ''}
                    </button>
                    <div class="flex flex-col min-w-0 w-full">
                        <div class="flex justify-between w-full">
                            <span class="text-xs font-medium truncate ${todo.is_completed ? 'line-through text-gray-400' : 'text-gray-700 dark:text-gray-200'}">${todo.title}</span>
                            <span class="${isUrgent && !todo.is_completed ? 'text-red-500 font-bold' : 'text-gray-400'} text-[9px] whitespace-nowrap">${dateStr}</span>
                        </div>
                        ${todo.description ? `
                            <div id="desc-container-${todo.id}" class="relative transition-all duration-300 overflow-hidden max-h-[1.2rem]">
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5 leading-4">${todo.description}</p>
                            </div>
                        ` : ''}
                    </div>
                    <button onclick="event.stopPropagation(); deleteTodo(${todo.id})" class="text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </div>
            </div>`;
    }

    function toggleTaskExpand(id) {
        const container = document.getElementById(`desc-container-${id}`);
        if(container) container.style.maxHeight = container.style.maxHeight === '150px' ? '1.2rem' : '150px';
    }

    function addTodo(e) {
        e.preventDefault();
        const title = document.getElementById('todo-title').value;
        const desc = document.getElementById('todo-desc').value;
        const date = $('#real-date-input').val();
        if (!date) { alert('لطفا زمان را انتخاب کنید'); return; }

        fetch(TODO_URL, {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN_TODO },
            body: JSON.stringify({ title, description: desc, due_date: date })
        }).then(res => {
            if(res.ok) {
                document.getElementById('todo-title').value = '';
                document.getElementById('todo-desc').value = '';
                $('#persian-date-input').val(''); $('#real-date-input').val('');
                fetchTodos();
            }
        });
    }

    function toggleTodo(id) {
        fetch(`${TODO_URL}/${id}`, { method: 'PATCH', headers: {'Content-Type': 'application/json','Accept': 'application/json','X-CSRF-TOKEN': CSRF_TOKEN_TODO} })
        .then(() => fetchTodos());
    }

    function deleteTodo(id) {
        if(confirm('حذف شود؟')) fetch(`${TODO_URL}/${id}`, { method: 'DELETE', headers: {'Content-Type': 'application/json','Accept': 'application/json','X-CSRF-TOKEN': CSRF_TOKEN_TODO} })
        .then(() => fetchTodos());
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


                <div class="flex items-center gap-3 mr-4">

    <div id="nav-weather-widget" class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 border border-blue-100 dark:bg-blue-900/20 dark:border-blue-800 text-blue-600 dark:text-blue-300 cursor-help" title="تبریز">
        <span id="nav-weather-icon" class="text-lg">⏳</span>
        <span id="nav-weather-temp" class="text-sm font-bold ltr">--°</span>
    </div>

    <div class="relative" id="todo-dropdown-wrapper">
        
        <button onclick="toggleTodoDropdown()" class="relative p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors group">
            <svg class="w-6 h-6 text-gray-600 dark:text-gray-300 group-hover:text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
            
            <span id="todo-badge" class="absolute top-1 right-1 h-3 w-3 bg-red-500 rounded-full border-2 border-white dark:border-dark-bg hidden animate-pulse"></span>
        </button>

        <div id="todo-dropdown-panel" 
     class="absolute top-full left-0 mt-4 w-[380px] bg-white dark:bg-[#2f3349] rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 transform scale-95 opacity-0 invisible transition-all duration-200 origin-top-left z-50 flex flex-col h-[500px]"> <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-3 rounded-t-2xl flex justify-between items-center text-white shadow-md z-10">
                <h3 class="font-bold text-sm">لیست کارهای من</h3>
                <div class="flex gap-2">
                    <button onclick="fetchTodos()" class="hover:bg-white/20 p-1 rounded-full"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg></button>
                    <button onclick="toggleTodoDropdown()" class="hover:bg-white/20 p-1 rounded-full"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
            </div>

            <div class="flex border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-dark-bg">
                <button onclick="switchTab('active')" id="tab-btn-active" class="flex-1 py-2 text-xs font-bold text-emerald-600 border-b-2 border-emerald-600">تسک‌ها</button>
                <button onclick="switchTab('done')" id="tab-btn-done" class="flex-1 py-2 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">انجام شده</button>
            </div>

            <div id="todo-input-container" class="p-3 bg-gray-50 dark:bg-dark-bg border-b border-gray-200 dark:border-gray-700">
                <form onsubmit="addTodo(event)" class="space-y-2">
                    <input type="text" id="todo-title" required class="w-full px-3 py-1.5 bg-white dark:bg-dark-paper border border-gray-300 dark:border-gray-600 rounded-lg text-xs focus:ring-1 focus:ring-emerald-500 dark:text-white" placeholder="عنوان کار...">
                    <textarea id="todo-desc" rows="1" class="w-full px-3 py-1.5 bg-white dark:bg-dark-paper border border-gray-300 dark:border-gray-600 rounded-lg text-xs focus:ring-1 focus:ring-emerald-500 dark:text-white resize-none" placeholder="توضیحات..."></textarea>
                    <div class="flex gap-2 relative">
                        <input type="text" id="persian-date-input" required class="flex-1 px-3 py-1.5 bg-white dark:bg-dark-paper border border-gray-300 dark:border-gray-600 rounded-lg text-xs dark:text-white" placeholder="زمان..." autocomplete="off">
                        <input type="hidden" id="real-date-input" name="due_date">
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium">افزودن</button>
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
                            {{ auth()->user()->name }}
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
            </nav>

            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>
        
    </div>

    <div id="global-toast-container" class="fixed bottom-6 left-6 z-50 flex flex-col gap-4"></div>

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

    <script>
    // --- Global Alert Logic (Session Aware) ---
    
    function showUrgentToast(taskId, taskTitle, timeText) {
        // چک کردن سشن: آیا در این نشست مرورگر، کاربر این هشدار را بسته است؟
        if (sessionStorage.getItem(`closed_alert_${taskId}`)) {
            return; 
        }

        if (document.getElementById(`toast-${taskId}`)) return;

        const container = document.getElementById('global-toast-container');
        const toast = document.createElement('div');
        toast.id = `toast-${taskId}`;
        toast.className = "toast-enter toast-idle bg-white dark:bg-[#2f3349] border-r-4 border-red-500 shadow-2xl rounded-lg p-4 w-80 flex items-start gap-3 relative overflow-hidden";
        
        toast.innerHTML = `
            <div class="absolute inset-0 bg-red-500 opacity-5 pointer-events-none"></div>
            <div class="bg-red-100 dark:bg-red-900/30 p-2 rounded-full text-red-600 dark:text-red-400 shrink-0 animate-pulse">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
            </div>
            <div class="flex-1">
                <h4 class="font-bold text-gray-800 dark:text-white text-sm">یادآوری مهم!</h4>
                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 leading-5">مهلت انجام کار <span class="font-bold text-red-500">"${taskTitle}"</span> نزدیک است.</p>
                <span class="text-[10px] text-gray-400 mt-2 block text-left dir-ltr">${timeText}</span>
            </div>
            <button onclick="closeToast(${taskId})" class="text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        `;
        container.appendChild(toast);
    }

    function closeToast(taskId) {
        const toast = document.getElementById(`toast-${taskId}`);
        if (toast) {
            toast.remove();
            // ذخیره می‌کنیم که "این هشدار بسته شد"
            sessionStorage.setItem(`closed_alert_${taskId}`, 'true');
        }
    }

    function checkUrgentTasksGlobal() {
        fetch('/admin/todos/check-urgent')
            .then(res => res.json())
            .then(tasks => {
                tasks.forEach(task => {
                    const time = new Date(task.due_date).toLocaleTimeString('fa-IR', {hour: '2-digit', minute:'2-digit'});
                    showUrgentToast(task.id, task.title, `زمان سررسید: ${time}`);
                });
            })
            .catch(() => {});
    }

    // --- Logout Handler (جدید) ---
    // وقتی کاربر دکمه خروج را می‌زند، سابقه هشدارهای بسته شده پاک می‌شود
    document.addEventListener('submit', (e) => {
        // چک می‌کنیم آیا فرمی که سابمیت شده مربوط به logout است؟
        if (e.target.action && e.target.action.includes('logout')) {
            // پاک کردن تمام کلیدهایی که با closed_alert_ شروع می‌شوند
            Object.keys(sessionStorage).forEach(key => {
                if (key.startsWith('closed_alert_')) {
                    sessionStorage.removeItem(key);
                }
            });
        }
    });

    document.addEventListener("DOMContentLoaded", () => {
        checkUrgentTasksGlobal();
        setInterval(checkUrgentTasksGlobal, 60000);
    });
</script>
</body>
</html>