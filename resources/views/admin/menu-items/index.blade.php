@extends('admin.layouts.app')
@section('title', 'مدیریت منوها')

@section('content')
<div dir="rtl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold dark:text-white">مدیریت منوها</h1>
        <a href="{{ route('admin.menu-items.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            + افزودن آیتم جدید
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-r-4 border-green-500 text-green-700 dark:bg-green-900/50 dark:text-green-300 dark:border-green-600 p-4 mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border-r-4 border-red-500 text-red-700 dark:bg-red-900/50 dark:text-red-300 dark:border-red-600 p-4 mb-4" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white dark:bg-dark-paper shadow-md rounded-lg overflow-hidden transition-colors">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">نام (متن لینک)</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">لینک (URL)</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">والد</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">گروه</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">ترتیب</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover"></th>
                </tr>
            </thead>
            <tbody class="text-gray-700 dark:text-gray-300">
                @forelse ($menuItems as $item)
                <tr class="hover:bg-gray-50 dark:hover:bg-dark-hover transition-colors {{ $item->parent_id ? 'bg-gray-50 dark:bg-gray-800' : 'bg-white dark:bg-dark-paper' }}">
                    
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 text-sm">
                        <p class="font-semibold text-gray-900 dark:text-white {{ $item->parent_id ? 'mr-4' : '' }}">
                            @if($item->parent_id) <span class="text-gray-400">&Larr;</span> @endif
                            {{ $item->name }}
                        </p>
                    </td>
                    
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 text-sm" dir="ltr">{{ $item->link_url }}</td>
                    
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 text-sm">{{ $item->parent->name ?? '---' }}</td>
                    
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 text-sm">{{ $item->menu_group }}</td>
                    
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 text-sm">{{ $item->order }}</td>
                    
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 text-sm text-left">
                        <a href="{{ route('admin.menu-items.edit', $item) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 ml-4 transition-colors">ویرایش</a>
                        
                        <form action="{{ route('admin.menu-items.destroy', $item) }}" method="POST" class="inline-block" onsubmit="return confirm('آیا از حذف این آیتم مطمئن هستید؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors">حذف</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-10 text-center text-gray-500 dark:text-gray-400 bg-white dark:bg-dark-paper border-b border-gray-200 dark:border-gray-700">
                        هیچ آیتم منویی تعریف نشده است.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection