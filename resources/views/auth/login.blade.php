@extends('layouts.app')

@section('title', 'ورود به پنل مدیریت')

@section('content')
<style>
    /* Hide Dashboard/Site Navigation Elements */
    nav, footer, header, aside { display: none !important; }
    
    /* Reset Main Content Area */
    main { padding: 0 !important; margin: 0 !important; width: 100% !important; max-width: 100% !important; }
    
    /* Ensure the body takes full height and uses the correct background */
    body { 
        display: flex; 
        flex-direction: column; 
        min-height: 100vh;
    }
</style>

<div class="w-full min-h-screen flex items-stretch overflow-hidden bg-white dark:bg-dark-bg transition-colors duration-300 flex-row-reverse" dir="rtl">
    
    <div class="hidden lg:flex lg:w-2/3 bg-gray-50 dark:bg-dark-paper items-center justify-center relative transition-colors duration-300">
        
        <div class="absolute w-full h-full bg-no-repeat bg-center opacity-10 dark:opacity-5" 
             style="background-image: url('https://cdn.jsdelivr.net/npm/hero-patterns@1.0.0/svgs/circuit-board.svg');">
        </div>

        <div class="z-10 text-center px-6">
            <img src="{{ asset('images/illus-gem3.png') }}" 
                 alt="Login Illustration Light" 
                 class="max-w-full lg:max-w-[600px] h-auto object-contain mx-auto transition-transform duration-500 hover:scale-105 block dark:hidden">
            
            <img src="{{ asset('images/illus-gem3.png') }}" 
                 alt="Login Illustration Dark" 
                 class="max-w-full lg:max-w-[600px] h-auto object-contain mx-auto transition-transform duration-500 hover:scale-105 hidden dark:block">
        </div>
    </div>

    <div class="w-full lg:w-1/3 flex items-center justify-center bg-white dark:bg-dark-bg px-8 py-12 sm:px-12 transition-colors duration-300 relative">
        <div class="w-full max-w-md space-y-8">
            
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white text-lg font-bold shadow-lg shadow-blue-600/30">
                    A
                </div>
                <span class="text-2xl font-bold text-gray-800 dark:text-gray-200 tracking-wide">آکامد</span>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">خوش آمدید! 👋</h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm"> برای ورود به پنل شماره موبایل خود را وارد کنید.</p>
            </div>

            @if(session('error'))
                <div class="bg-red-50 text-red-500 dark:bg-red-900/20 dark:text-red-300 text-sm p-4 rounded-lg flex items-center gap-3 shadow-sm border border-red-100 dark:border-red-900/30">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('error') }}
                </div>
            @endif
            @if(session('success'))
                <div class="bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-300 text-sm p-4 rounded-lg flex items-center gap-3 shadow-sm border border-green-100 dark:border-green-900/30">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('otp.send') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="mobile" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">شماره موبایل</label>
                    <input id="mobile" type="tel" name="mobile" value="{{ old('mobile') }}" 
                           required autofocus autocomplete="tel"
                           class="w-full px-4 py-3 bg-white dark:bg-dark-paper border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block transition-colors duration-200 placeholder-gray-400 dark:placeholder-gray-500"
                           placeholder="09123456789" dir="ltr">
                    @error('mobile')
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox" 
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 dark:bg-dark-paper rounded cursor-pointer">
                        <label for="remember-me" class="mr-2 block text-sm text-gray-600 dark:text-gray-400 cursor-pointer select-none">
                            مرا به خاطر بسپار
                        </label>
                    </div>
                </div> -->

                <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 transform active:scale-[0.98]">
                    ارسال کد تایید
                </button>
            </form>

            <div class="mt-8">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-3 bg-white dark:bg-dark-bg text-gray-500 dark:text-gray-400 font-medium">یا ورود با</span>
                    </div>
                </div>

                <div class="mt-6 flex justify-center gap-4">
                    <a href="#" class="w-10 h-10 rounded-lg bg-[#d62d20]/10 dark:bg-[#d62d20]/20 text-[#d62d20] flex items-center justify-center transition-all hover:bg-[#d62d20]/20 hover:scale-110">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0 5.867 0 .307 5.387.307 12s5.56 12 12.173 12c3.573 0 6.267-1.173 8.373-3.36 2.16-2.16 2.84-5.213 2.84-7.667 0-.76-.053-1.467-.173-2.053H12.48z"/></svg>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-lg bg-[#1877F2]/10 dark:bg-[#1877F2]/20 text-[#1877F2] flex items-center justify-center transition-all hover:bg-[#1877F2]/20 hover:scale-110">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-lg bg-[#1da1f2]/10 dark:bg-[#1da1f2]/20 text-[#1da1f2] flex items-center justify-center transition-all hover:bg-[#1da1f2]/20 hover:scale-110">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                    </a>
                </div>
            </div>


        </div>
    </div>
</div>
@endsection