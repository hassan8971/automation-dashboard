@extends('admin.layouts.app')

@section('title', 'ویرایش اشتراک')

@section('content')
<div dir="rtl" class="max-w-4xl mx-auto">
    {{-- هدر صفحه --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold dark:text-white">
            ویرایش اشتراک: 
            <span class="text-blue-600 dark:text-blue-400 text-lg">{{ $subscription->name }}</span>
        </h1>
        <a href="{{ route('admin.subscriptions.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 text-sm flex items-center gap-1 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
            بازگشت به لیست
        </a>
    </div>

    {{-- کارت فرم --}}
    <div class="bg-white dark:bg-dark-paper shadow-lg rounded-xl p-6 sm:p-8 transition-colors border border-gray-100 dark:border-gray-700">
        <form action="{{ route('admin.subscriptions.update', $subscription) }}" method="POST">
            @csrf
            @method('PUT')
            
            {{-- فراخوانی فرم مشترک --}}
            @include('admin.subscriptions._form', ['subscription' => $subscription])
        </form>
    </div>
</div>
@endsection