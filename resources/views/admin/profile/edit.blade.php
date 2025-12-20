@extends('admin.layouts.app')

@section('title', 'ویرایش پروفایل')

@section('content')
<div class="max-w-4xl mx-auto">
    
    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-100 border border-emerald-200 text-emerald-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-dark-paper rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
        
        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">تنظیمات پروفایل</h2>
            <p class="text-sm text-gray-500 mt-1">اطلاعات شخصی و ظاهری پنل خود را ویرایش کنید.</p>
        </div>

        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <div class="flex flex-col items-center gap-4">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">عکس پروفایل</span>
                    <div class="relative group w-32 h-32">
                        <img src="{{ $user->profile_photo_path ? asset('storage/'.$user->profile_photo_path) : 'https://ui-avatars.com/api/?name='.$user->name.'&background=random' }}" 
                             class="w-full h-full rounded-full object-cover border-4 border-gray-100 dark:border-gray-600 shadow-md">
                        <label class="absolute inset-0 flex items-center justify-center bg-black/50 text-white rounded-full opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <input type="file" name="profile_photo" class="hidden" accept="image/*">
                        </label>
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">بنر داشبورد</span>
                    <div class="relative group w-full h-32 rounded-xl overflow-hidden border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-emerald-500 transition-colors bg-gray-50 dark:bg-dark-bg">
                        @if($user->dashboard_banner_path)
                            <img src="{{ asset('storage/'.$user->dashboard_banner_path) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <span class="text-xs">هنوز بنری آپلود نشده</span>
                            </div>
                        @endif
                        
                        <label class="absolute inset-0 flex items-center justify-center bg-black/50 text-white opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity">
                            <span class="text-sm font-medium">تغییر بنر</span>
                            <input type="file" name="dashboard_banner" class="hidden" accept="image/*">
                        </label>
                    </div>
                    <p class="text-xs text-gray-500">سایز پیشنهادی: 1500x400 پیکسل</p>
                </div>
            </div>

            <hr class="border-gray-100 dark:border-gray-700">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">نام</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-dark-bg dark:text-white focus:ring-2 focus:ring-emerald-500 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">نام خانوادگی</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-dark-bg dark:text-white focus:ring-2 focus:ring-emerald-500 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">نام مستعار (Nickname)</label>
                    <input type="text" name="nickname" value="{{ old('nickname', $user->nickname) }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-dark-bg dark:text-white focus:ring-2 focus:ring-emerald-500 transition-all">
                </div>

                <div class="flex items-center h-full pt-6">
                    <label class="flex items-center cursor-pointer gap-3">
                        <div class="relative">
                            <input type="checkbox" name="use_nickname" class="sr-only peer" {{ $user->use_nickname ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 dark:peer-focus:ring-emerald-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-emerald-600"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">نمایش نام مستعار در پنل به جای نام اصلی</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-lg shadow-lg shadow-emerald-500/30 transition-all transform hover:-translate-y-1">
                    ذخیره تغییرات
                </button>
            </div>

        </form>
    </div>
</div>
@endsection