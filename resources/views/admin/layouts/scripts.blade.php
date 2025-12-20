<script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="https://unpkg.com/persian-date@1.1.0/dist/persian-date.min.js"></script>
    <script src="https://unpkg.com/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js"></script>

<script>
    // --- CONFIG ---
    const TODO_URL = "{{ route('admin.todos.index') }}";
    const CSRF_TOKEN_TODO = "{{ csrf_token() }}";
    
    // --- 1. GREETING & WEATHER LOGIC (With 30-min Cache) ---
    document.addEventListener("DOMContentLoaded", () => {
        const CACHE_KEY = 'admin_weather_data';
        const CACHE_DURATION = 30 * 60 * 1000; // 30 Minutes

        // Helper to update the DOM
        function updateWeatherUI(temp, code) {
            const hour = new Date().getHours();
            const isDay = hour > 6 && hour < 19;
            let wIcon = '🌡️';

            // Icon Mapping
            if (code === 0) wIcon = isDay ? '☀️' : '🌙';
            else if (code <= 3) wIcon = isDay ? '⛅' : '☁️';
            else if (code >= 45 && code <= 48) wIcon = '🌫️';
            else if (code >= 51 && code <= 67) wIcon = '🌧️';
            else if (code >= 71 && code <= 86) wIcon = '❄️';
            else if (code >= 95) wIcon = '⛈️';

            const tempEl = document.getElementById('nav-weather-temp');
            const iconEl = document.getElementById('nav-weather-icon');
            
            if(tempEl) tempEl.innerText = temp + '°C';
            if(iconEl) iconEl.innerText = wIcon;
        }

        // 1. Try to load from Cache
        const cached = localStorage.getItem(CACHE_KEY);
        let validCache = false;

        if (cached) {
            try {
                const data = JSON.parse(cached);
                const now = new Date().getTime();
                // Check if cache is still valid
                if (now < data.expiry) {
                    updateWeatherUI(data.temp, data.code);
                    validCache = true;
                }
            } catch (e) {
                console.error("Cache parse error", e);
            }
        }

        // 2. Fetch from API if cache is invalid/missing
        if (!validCache) {
            fetch('https://api.open-meteo.com/v1/forecast?latitude=38.08&longitude=46.29&current_weather=true')
                .then(res => res.json())
                .then(data => {
                    const temp = Math.round(data.current_weather.temperature);
                    const code = data.current_weather.weathercode;

                    // Update UI
                    updateWeatherUI(temp, code);

                    // Save to LocalStorage
                    const cacheData = {
                        temp: temp,
                        code: code,
                        expiry: new Date().getTime() + CACHE_DURATION
                    };
                    localStorage.setItem(CACHE_KEY, JSON.stringify(cacheData));
                })
                .catch((err) => {
                    console.error('Weather Error:', err);
                    const tempEl = document.getElementById('nav-weather-temp');
                    if(tempEl) tempEl.innerText = 'N/A';
                });
        }
            
        // Init Todos
        if(typeof fetchTodos === 'function') {
            fetchTodos();
        }
    });

    // --- 2. DROPDOWN TOGGLE LOGIC ---
    function toggleTodoDropdown() {
        const panel = document.getElementById('todo-dropdown-panel');
        closeAllDropdowns('todo'); // Close others
        if(panel) togglePanel(panel);
    }

    function toggleProfileDropdown() {
        const panel = document.getElementById('profile-dropdown-panel');
        closeAllDropdowns('profile'); // Close others
        if(panel) togglePanel(panel);
    }

    function togglePanel(panel) {
        if (panel.classList.contains('invisible')) {
            panel.classList.remove('invisible', 'opacity-0', 'scale-95');
            panel.classList.add('visible', 'opacity-100', 'scale-100');
        } else {
            panel.classList.add('invisible', 'opacity-0', 'scale-95');
            panel.classList.remove('visible', 'opacity-100', 'scale-100');
        }
    }

    function closeAllDropdowns(except = null) {
        if(except !== 'todo') closePanel('todo-dropdown-panel');
        if(except !== 'profile') closePanel('profile-dropdown-panel');
    }

    function closePanel(id) {
        const panel = document.getElementById(id);
        if(panel) {
            panel.classList.add('invisible', 'opacity-0', 'scale-95');
            panel.classList.remove('visible', 'opacity-100', 'scale-100');
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        // Todo
        const todoWrapper = document.getElementById('todo-dropdown-wrapper');
        const isDatepicker = event.target.closest('.datepicker-plot-area') || event.target.closest('.datepicker-container');
        if (todoWrapper && !todoWrapper.contains(event.target) && !isDatepicker) {
             closePanel('todo-dropdown-panel');
        }

        // Profile
        const profileWrapper = document.getElementById('profile-dropdown-wrapper');
        if (profileWrapper && !profileWrapper.contains(event.target)) {
            closePanel('profile-dropdown-panel');
        }

        // Theme
        const themeBtn = document.getElementById('theme-toggle-btn');
        const themeDrop = document.getElementById('theme-dropdown');
        if (themeBtn && themeDrop && !themeBtn.contains(event.target) && !themeDrop.contains(event.target)) {
             themeDrop.classList.add('fade-enter');
             themeDrop.classList.remove('fade-enter-active');
        }
    });

    // --- 3. TODO WIDGET LOGIC ---
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