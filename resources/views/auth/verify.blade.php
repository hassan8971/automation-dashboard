@extends('layouts.app')

@section('title', 'تایید شماره موبایل')

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
                 class="max-w-full lg:max-w-[600px] h-auto object-contain mx-auto transition-transform duration-500 hover:scale-105 hidden dark:block">       </div>
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
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">تایید دو مرحله‌ای 💬</h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm leading-6">
                    کد تایید ۴ رقمی به شماره 
                    <span class="font-bold text-gray-800 dark:text-white" dir="ltr">{{ $mobile ?? session('mobile') }}</span>
                    ارسال شد.
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">(کد تست: 1234)</p>
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

            <form method="POST" action="{{ route('otp.verify') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">کد تایید را وارد کنید</label>
                    
                    <input id="code" type="tel" name="code" 
                           required autofocus maxlength="4"
                           class="w-full px-4 py-3 bg-white dark:bg-dark-paper border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-xl font-bold tracking-[1em] text-center rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block transition-colors duration-200 placeholder-gray-300 dark:placeholder-gray-600"
                           placeholder="----" dir="ltr">
                    @error('code')
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 transform active:scale-[0.98]">
                    ورود و تایید
                </button>
            </form>

            <div class="text-center mt-6">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    شماره اشتباه است؟ 
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 font-medium transition-colors">
                        ویرایش شماره موبایل
                    </a>
                </p>
            </div>

        </div>
    </div>
</div>
@endsection