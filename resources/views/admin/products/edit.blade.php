@extends('admin.layouts.app')

@section('title', 'ویرایش اپلیکیشن')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div dir="rtl" class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold dark:text-white">
            ویرایش اپلیکیشن: <span class="text-blue-600">{{ $product->title }}</span>
        </h1>
        <a href="{{ route('admin.products.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 text-sm flex items-center gap-1">
            بازگشت به لیست
        </a>
    </div>

    <div class="bg-white dark:bg-dark-paper shadow-lg rounded-xl p-6 border border-gray-100 dark:border-gray-700">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            {{-- فراخوانی فرم مشترک --}}
            @include('admin.products._form', ['product' => $product])
        </form>
    </div>
</div>
@endsection