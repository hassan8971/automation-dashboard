@extends('admin.layouts.app')

@section('title', 'ایجاد افزونه جدید')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<div dir="rtl" class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold dark:text-white">ایجاد افزونه جدید</h1>
        <a href="{{ route('admin.addons.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 text-sm flex items-center gap-1 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            بازگشت به لیست
        </a>
    </div>

    <div class="bg-white dark:bg-dark-paper shadow-lg rounded-xl p-6 sm:p-8 transition-colors border border-gray-100 dark:border-gray-700">
        <form action="{{ route('admin.addons.store') }}" method="POST">
            @csrf
            
            {{-- We pass null so the form knows it is in Create mode --}}
            @include('admin.addons._form', ['addon' => null])
        </form>
    </div>
</div>
@endsection