@extends('admin.layouts.app')
@section('title', 'ایجاد Redeem Code')
@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<div dir="rtl" class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold dark:text-white">ایجاد Redeem Code</h1>
        <a href="{{ route('admin.redeem-codes.index') }}" class="text-sm text-gray-500 hover:text-gray-700">بازگشت</a>
    </div>
    <div class="bg-white dark:bg-dark-paper shadow rounded-xl p-6 border dark:border-gray-700">
        <form action="{{ route('admin.redeem-codes.store') }}" method="POST">
            @csrf
            @include('admin.redeem-codes._form', ['redeemCode' => null])
        </form>
    </div>
</div>
@endsection