@props(['title', 'active' => false])

<li>
    <div class="sidebar-toggle flex items-center cursor-pointer space-x-3 space-x-reverse px-4 py-2 rounded-lg text-gray-200 sidebar-menu-item hover:text-white hover:bg-gray-700 transition-colors
            {{ $active ? 'active-menu-item text-white bg-gray-700' : '' }}">
        
        <span class="shrink-0">
            {{ $icon }}
        </span>
        
        <span class="sidebar-text">{{ $title }}</span>
        
        <span class="sidebar-arrow arrow block transition-transform duration-500 !mr-auto 
              {{ $active ? 'rotate-90' : 'rotate-180' }}">
             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </span>
    </div>

    <ul class="sidebar-submenu overflow-hidden transition-all duration-500 ease-in-out rounded-lg
               {{ $active ? '' : 'max-h-0' }}">
        {{ $slot }}
    </ul>
</li>