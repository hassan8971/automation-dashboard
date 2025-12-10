<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'فروشگاه من')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // --- THIS SCRIPT ENABLES DARK MODE BASED ON SAVED PREFERENCE ---
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }

        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: {
                            bg: '#25293c',      // Dark Body Background
                            paper: '#2f3349',   // Dark Card Background
                            text: '#cfcde4',    // Dark Text Color
                            hover: '#3a3b64',   // Dark Hover State
                            border: '#4b4b4b'   // Dark Border
                        }
                    }
                }
            }
        }
    </script>
    <style>
       
        .group:hover .group-hover\:block { display: block; }
        
        /* Smooth color transition for all elements */
        * { transition-property: background-color, border-color, color, fill, stroke; transition-duration: 300ms; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 text-gray-900 dark:bg-dark-bg dark:text-dark-text">

    <nav class="bg-white dark:bg-dark-paper shadow-md" x-data="{ open: false }">
        </nav>

    <main>
        @yield('content')
    </main>

    <footer class="bg-white dark:bg-dark-paper mt-12 border-t border-gray-200 dark:border-dark-border">
        </footer>

</body>
</html>