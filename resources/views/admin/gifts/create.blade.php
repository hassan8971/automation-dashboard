@extends('admin.layouts.app')

@section('title', 'تعریف گیفت جدید')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div dir="rtl" class="max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold dark:text-white">تعریف گیفت جدید</h1>
        <a href="{{ route('admin.gifts.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 text-sm">بازگشت</a>
    </div>

    <div class="bg-white dark:bg-dark-paper shadow-lg rounded-xl p-6 border border-gray-100 dark:border-gray-700">
        <form action="{{ route('admin.gifts.store') }}" method="POST">
            @csrf
            
            {{-- پاس دادن null برای متغیر gift تا فرم بفهمد حالت ایجاد است --}}
            @include('admin.gifts._form', ['gift' => null])
        </form>
    </div>
</div>
@endsection