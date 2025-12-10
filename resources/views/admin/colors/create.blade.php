@extends('admin.layouts.app')
@section('title', 'افزودن رنگ جدید')

@section('content')
<div dir="rtl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold dark:text-white">افزودن رنگ جدید</h1>
        
        <a href="{{ route('admin.colors.index') }}" class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-dark-hover transition-colors">
            &larr; بازگشت
        </a>
    </div>

    <form action="{{ route('admin.colors.store') }}" method="POST">
        @csrf
        @include('admin.colors._form')
    </form>
</div>
@endsection