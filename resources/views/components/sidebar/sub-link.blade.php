@props(['href', 'active' => false, 'title'])

<li>
    <a href="{{ $href }}" 
       class="flex items-center space-x-3 space-x-reverse px-4 py-2 mt-2 rounded-lg text-gray-400 sidebar-menu-item hover:text-white
       {{ $active ? 'active-menu-item sub text-white' : '' }}"> <span class="w-2 h-2 rounded-full border border-gray-500 shrink-0"></span>
        <span class="sidebar-text">{{ $title }}</span>    
    </a>
</li>