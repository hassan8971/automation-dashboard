@extends('admin.layouts.app')
@section('title', 'کتابخانه ویدیوها')

@section('content')
<div dir="rtl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold dark:text-white">کتابخانه ویدیوها</h1>
        <a href="{{ route('admin.videos.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            + افزودن ویدیوی جدید
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-r-4 border-green-500 text-green-700 dark:bg-green-900/50 dark:text-green-300 dark:border-green-600 p-4 mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-dark-paper shadow-md rounded-lg overflow-hidden transition-colors">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover text-right text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">ویدیو</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover text-right text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">نام (برای ادمین)</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover text-right text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">نوع</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover"></th>
                </tr>
            </thead>
            <tbody class="text-gray-700 dark:text-gray-300">
                @forelse ($videos as $video)
                <tr class="hover:bg-gray-50 dark:hover:bg-dark-hover transition-colors">
                    
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-dark-paper text-sm">
                        @if($video->type == 'embed')
                            <div class="w-32 h-20 bg-gray-800 text-white flex items-center justify-center rounded">Embed</div>
                        @else
                            <video class="w-32 h-20 rounded bg-gray-800" controls>
                                <source src="{{ Storage::url($video->path) }}" type="video/mp4">
                            </video>
                        @endif
                    </td>
                    
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-dark-paper text-sm text-gray-900 dark:text-white">{{ $video->name }}</td>
                    
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-dark-paper text-sm text-gray-900 dark:text-white">
                        {{ $video->type == 'upload' ? 'آپلودی' : 'الصاقی' }}
                    </td>
                    
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-dark-paper text-sm text-left">
                        <a href="{{ route('admin.videos.edit', $video) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 ml-4 transition-colors">ویرایش</a>
                        
                        <form action="{{ route('admin.videos.destroy', $video) }}" method="POST" class="inline-block" onsubmit="return confirm('آیا از حذف این ویدیو مطمئن هستید؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors">حذف</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-10 text-center text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-dark-paper">
                        هیچ ویدیویی در کتابخانه یافت نشد.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection