@extends('admin.layouts.app')

@section('title', 'مدیریت Redeem Code')

@section('content')
<div dir="rtl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold dark:text-white">کدهای هدیه / Redeem Codes</h1>
        <a href="{{ route('admin.redeem-codes.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-lg">
            + ایجاد کد جدید
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-r-4 border-green-500 text-green-700 dark:bg-green-900/50 dark:text-green-300 p-4 mb-4 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-dark-paper shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead class="bg-gray-100 dark:bg-dark-hover">
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">کد</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">مبلغ (تومان)</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">سرویس</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">مصرف</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">انقضا</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">وضعیت</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700"></th>
                </tr>
            </thead>
            <tbody class="dark:text-gray-200">
                @foreach($codes as $code)
                <tr class="hover:bg-gray-50 dark:hover:bg-dark-hover transition-colors">
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 font-mono font-bold text-blue-600 dark:text-blue-400">
                        {{ $code->code }}
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        {{ number_format($code->amount) }}
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        <span class="px-2 py-1 text-xs rounded bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                            {{ \App\Models\RedeemCode::SERVICES[$code->service_type] ?? $code->service_type }}
                        </span>
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        <span class="{{ $code->used_count >= $code->usage_limit ? 'text-red-500' : 'text-green-500' }}">
                            {{ $code->used_count }} / {{ $code->usage_limit }}
                        </span>
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 text-sm">
                        @if($code->expires_at)
                            {{ $code->expires_at->format('Y-m-d') }}
                            <br>
                            <span class="text-xs text-gray-400">
                                ({{ $code->expires_at->diffForHumans() }})
                            </span>
                        @else
                            <span class="text-gray-400">--</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        @if($code->is_active && ($code->used_count < $code->usage_limit))
                            <span class="text-green-600 text-xs font-bold">فعال</span>
                        @else
                            <span class="text-red-600 text-xs font-bold">غیرفعال/منقضی</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 text-left whitespace-nowrap">
                        <a href="{{ route('admin.redeem-codes.edit', $code) }}" class="text-blue-600 hover:text-blue-800 ml-3 text-sm">ویرایش</a>
                        <form action="{{ route('admin.redeem-codes.destroy', $code) }}" method="POST" class="inline-block" onsubmit="return confirm('حذف شود؟')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700 text-sm">حذف</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 bg-gray-50 dark:bg-dark-bg">
            {{ $codes->links() }}
        </div>
    </div>
</div>
@endsection