@extends('admin.layouts.app')

@section('title', 'ویرایش تب')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">ویرایش تب: {{ $appTab->title }}</h1>
        <a href="{{ route('admin.app-tabs.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400">
            <i class="fas fa-arrow-right ml-1"></i> بازگشت
        </a>
    </div>

    <div class="bg-white dark:bg-dark-paper shadow-lg rounded-xl p-6 border dark:border-gray-700">
        <form action="{{ route('admin.app-tabs.update', $appTab->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.app_tabs._form', ['appTab' => $appTab])
        </form>
    </div>
</div>
@endsection