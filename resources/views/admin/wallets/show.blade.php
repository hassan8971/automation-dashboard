@extends('admin.layouts.app')

@section('title', 'جزئیات کیف پول')

@section('content')
<div dir="rtl" x-data="{ openModal: false, actionType: 'add' }">
    
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold dark:text-white">
                کیف پول: {{ $wallet->user->name }} 
                <span class="text-sm bg-gray-200 text-gray-700 px-2 py-1 rounded mx-2">{{ $wallet->service_name }}</span>
            </h1>
            <p class="text-gray-500 mt-1">موجودی فعلی: <span class="text-emerald-600 font-bold text-xl">{{ number_format($wallet->balance) }} تومان</span></p>
        </div>
        
        <div class="flex gap-2">
            <button @click="openModal = true; actionType = 'add'" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow flex items-center gap-2">
                <span>+</span> افزایش موجودی
            </button>
            <button @click="openModal = true; actionType = 'sub'" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow flex items-center gap-2">
                <span>-</span> کسر موجودی
            </button>
            <a href="{{ route('admin.wallets.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded shadow">بازگشت</a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 mb-6 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 mb-6 rounded">{{ session('error') }}</div>
    @endif

    {{-- Transactions Table --}}
    <div class="bg-white dark:bg-dark-paper shadow rounded-lg overflow-hidden">
        <h3 class="p-4 font-bold border-b bg-gray-50 dark:bg-dark-hover dark:text-white">تاریخچه تراکنش‌ها</h3>
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">نوع</th>
                    <th class="px-5 py-3 border-b-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">مبلغ</th>
                    <th class="px-5 py-3 border-b-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">وضعیت</th>
                    <th class="px-5 py-3 border-b-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">توضیحات</th>
                    <th class="px-5 py-3 border-b-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">تاریخ</th>
                </tr>
            </thead>
            <tbody class="dark:text-gray-200">
                @foreach($transactions as $trans)
                <tr class="hover:bg-gray-50 dark:hover:bg-dark-hover">
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        @if(in_array($trans->type, ['deposit', 'manual_add']))
                            <span class="text-green-600 font-bold">واریز</span>
                            @if($trans->type == 'manual_add') <span class="text-xs text-gray-400">(دستی)</span> @endif
                        @else
                            <span class="text-red-600 font-bold">برداشت</span>
                            @if($trans->type == 'manual_sub') <span class="text-xs text-gray-400">(دستی)</span> @endif
                        @endif
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 font-mono">
                        {{ number_format($trans->amount) }}
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        @if($trans->status == 'confirmed')
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">موفق</span>
                        @elseif($trans->status == 'pending')
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">در انتظار</span>
                        @else
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">ناموفق</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 text-sm">
                        {{ $trans->description }}
                        @if($trans->reference_id) <br><span class="text-xs text-gray-400">Ref: {{ $trans->reference_id }}</span> @endif
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 text-sm">
                        {{ $trans->created_at->format('Y-m-d H:i') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">
            {{ $transactions->links() }}
        </div>
    </div>

    {{-- Modal (AlpineJS) --}}
    <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="openModal = false"></div>

            <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-right align-middle transition-all transform bg-white dark:bg-dark-paper shadow-xl rounded-2xl">
                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white" x-text="actionType === 'add' ? 'افزایش موجودی دستی' : 'کسر موجودی دستی'"></h3>
                
                <form action="{{ route('admin.wallets.update-balance', $wallet) }}" method="POST" class="mt-4">
                    @csrf
                    <input type="hidden" name="type" x-model="actionType">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">مبلغ (تومان)</label>
                        <input type="number" name="amount" class="w-full border rounded px-3 py-2 dark:bg-dark-bg dark:text-white" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">توضیحات (علت)</label>
                        <textarea name="description" rows="2" class="w-full border rounded px-3 py-2 dark:bg-dark-bg dark:text-white" placeholder="مثال: هدیه جشنواره..."></textarea>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" @click="openModal = false" class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">انصراف</button>
                        <button type="submit" class="px-4 py-2 text-white rounded" :class="actionType === 'add' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'">ثبت</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection