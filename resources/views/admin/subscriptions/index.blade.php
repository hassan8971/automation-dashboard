@extends('admin.layouts.app')

@section('title', 'مدیریت اشتراک‌ها')

@section('content')
<div dir="rtl">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold dark:text-white">اشتراک‌ها</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                لیست تمام پلن‌های اشتراک موجود در اپلیکیشن
            </p>
        </div>
        <a href="{{ route('admin.subscriptions.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-lg">
            + ایجاد اشتراک جدید
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-r-4 border-green-500 text-green-700 dark:bg-green-900/50 dark:text-green-300 dark:border-green-600 p-4 mb-4 rounded-md shadow-sm" role="alert">
            <strong class="font-bold ml-2">موفقیت!</strong>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white dark:bg-dark-paper shadow-md rounded-lg overflow-hidden transition-colors">
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead class="bg-gray-100 dark:bg-dark-hover">
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">نام پلن</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">قیمت (تومان)</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">مدت (روز)</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">هدیه همراه</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">وضعیت</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">عملیات</th>
                    </tr>
                </thead>
                <tbody class="dark:text-gray-200">
                    @forelse($subscriptions as $subscription)
                    <tr class="hover:bg-gray-50 dark:hover:bg-dark-hover transition-colors">
                        <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                            <p class="text-gray-900 dark:text-white font-medium">{{ $subscription->name }}</p>
                            @if($subscription->gift_description)
                                <span class="text-xs text-gray-500 dark:text-gray-400 block mt-1">🎁 {{ $subscription->gift_description }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ number_format($subscription->price) }}</span>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                            {{ $subscription->duration_in_days }} روز
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                            @if($subscription->gift)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300 border border-purple-200 dark:border-purple-700">
                                    🎁 {{ $subscription->gift->title }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400 italic">انتخاب نشده</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                            @if($subscription->is_active)
                                <span class="relative inline-block px-3 py-1 font-semibold text-green-900 dark:text-green-200 leading-tight">
                                    <span aria-hidden class="absolute inset-0 bg-green-200 dark:bg-green-900 opacity-50 rounded-full"></span>
                                    <span class="relative">فعال</span>
                                </span>
                            @else
                                <span class="relative inline-block px-3 py-1 font-semibold text-red-900 dark:text-red-200 leading-tight">
                                    <span aria-hidden class="absolute inset-0 bg-red-200 dark:bg-red-900 opacity-50 rounded-full"></span>
                                    <span class="relative">غیرفعال</span>
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 whitespace-nowrap">
                            <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 transition-colors ml-4">ویرایش</a>
                            <form action="{{ route('admin.subscriptions.destroy', $subscription) }}" method="POST" class="inline-block" onsubmit="return confirm('آیا از حذف این اشتراک مطمئن هستید؟');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors">حذف</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-gray-500 dark:text-gray-400 bg-white dark:bg-dark-paper">
                            هیچ پلن اشتراکی یافت نشد. <a href="{{ route('admin.subscriptions.create') }}" class="text-blue-600 dark:text-blue-400 hover:underline">یکی ایجاد کنید!</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection