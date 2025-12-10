@extends('admin.layouts.app')
@section('title', 'مدیریت کدهای تخفیف')

@section('content')
<div dir="rtl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold dark:text-white">مدیریت کدهای تخفیف</h1>
        <a href="{{ route('admin.discounts.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            + افزودن کد تخفیف
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-r-4 border-green-500 text-green-700 dark:bg-green-900/50 dark:text-green-300 dark:border-green-600 p-4 mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border-r-4 border-red-500 text-red-700 dark:bg-red-900/50 dark:text-red-300 dark:border-red-600 p-4 mb-4" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white dark:bg-dark-paper shadow-md rounded-lg overflow-hidden transition-colors">
        <table class="min-w-full leading-normal">
            <thead class="bg-gray-100 dark:bg-dark-hover">
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">نام</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">کد</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">نوع</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">مقدار</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">استفاده شده</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">وضعیت</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700"></th>
                </tr>
            </thead>
            <tbody class="text-gray-700 dark:text-gray-300">
                @forelse ($discounts as $discount)
                <tr class="hover:bg-gray-50 dark:hover:bg-dark-hover transition-colors">
                    
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-dark-paper text-sm">
                        <span class="font-bold text-gray-900 dark:text-white">{{ $discount->name }}</span>
                    </td>
                    
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-dark-paper text-sm">
                        <span class="font-bold text-gray-900 dark:text-white" dir="ltr">{{ $discount->code }}</span>
                    </td>
                    
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-dark-paper text-sm">
                        {{ $discount->type === 'percent' ? 'درصدی' : 'مبلغ ثابت' }}
                    </td>
                    
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-dark-paper text-sm">
                        @if($discount->type === 'percent')
                            {{ $discount->value }}%
                        @else
                            {{ number_format($discount->value) }} تومان
                        @endif
                    </td>
                    
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-dark-paper text-sm">
                        {{ $discount->times_used }} / {{ $discount->usage_limit ?? '∞' }}
                    </td>
                    
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-dark-paper text-sm">
                        @if(!$discount->is_active)
                            <span class="px-2 py-1 font-semibold leading-tight text-gray-700 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-300">غیرفعال</span>
                        @elseif($discount->expires_at && $discount->expires_at->isPast())
                            <span class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full dark:bg-red-900/50 dark:text-red-300">منقضی شده</span>
                        @else
                            <span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full dark:bg-green-900/50 dark:text-green-300">فعال</span>
                        @endif
                    </td>
                    
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-dark-paper text-sm text-left">
                        <a href="{{ route('admin.discounts.edit', $discount) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 ml-4 transition-colors">ویرایش</a>
                        
                        <form action="{{ route('admin.discounts.destroy', $discount) }}" method="POST" class="inline-block" onsubmit="return confirm('آیا از حذف این کد تخفیف مطمئن هستید؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors">حذف</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-10 text-center text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-dark-paper">
                        هیچ کد تخفیفی تعریف نشده است.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection