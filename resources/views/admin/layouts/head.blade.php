<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <style>
    /* ... your existing styles ... */

    /* Animation for the Aurora Blobs */
    @keyframes blob {
        0% { transform: translate(0px, 0px) scale(1); }
        33% { transform: translate(30px, -50px) scale(1.1); }
        66% { transform: translate(-20px, 20px) scale(0.9); }
        100% { transform: translate(0px, 0px) scale(1); }
    }
    .animate-blob {
        animation: blob 7s infinite;
    }
    .animation-delay-2000 {
        animation-delay: 2s;
    }
    .animation-delay-4000 {
        animation-delay: 4s;
    }
</style>
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

@stack('styles')