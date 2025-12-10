@props(['href', 'active' => false, 'title'])

<a href="{{ $href }}" 
   class="flex items-center space-x-3 space-x-reverse px-4 py-2 rounded-lg text-gray-200 dark:text-dark-text sidebar-menu-item hover:text-white hover:bg-gray-700 dark:hover:bg-dark-hover transition-colors
          {{ $active ? 'active-menu-item text-white bg-gray-700 dark:bg-dark-hover' : '' }}">
    
    <span class="shrink-0">
        {{ $slot }}
    </span>

    <span class="sidebar-text">{{ $title }}</span>
</a>