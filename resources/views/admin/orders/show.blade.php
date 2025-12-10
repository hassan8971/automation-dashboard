@extends('admin.layouts.app')

@section('title', 'جزئیات سفارش ' . $order->order_code)

@section('content')
<div dir="rtl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold dark:text-white">
            جزئیات سفارش <span class="text-gray-500 dark:text-gray-400">{{ $order->order_code }}</span>
        </h1>
        <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-dark-hover transition-colors">
            &larr; بازگشت به لیست سفارشات
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 mb-4 dark:bg-green-900/50 dark:text-green-300 dark:border-green-600" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-8">
            
            <div class="bg-white dark:bg-dark-paper shadow-md rounded-lg overflow-hidden transition-colors">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold dark:text-white">آیتم‌های سفارش ({{ $order->items->count() }})</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 dark:bg-dark-hover">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500 dark:text-gray-300">محصول</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500 dark:text-gray-300">قیمت واحد</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500 dark:text-gray-300">تعداد</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-300">مجموع</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($order->items as $item)
                                @php
                                    $variant = $item->productVariant; 
                                    $product = $variant ? $variant->product : null;
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-dark-hover transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-16 w-16 border border-gray-200 dark:border-gray-700 rounded-md overflow-hidden ml-4">
                                                @if($product && $product->images->isNotEmpty())
                                                    <img src="{{ Storage::url($product->images->first()->path) }}" 
                                                         alt="{{ $product->name }}" 
                                                         class="h-full w-full object-cover">
                                                @else
                                                    <div class="h-full w-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-xs text-gray-400 dark:text-gray-400">
                                                        بدون تصویر
                                                    </div>
                                                @endif
                                            </div>

                                            <div>
                                                @if($product)
                                                    <a href="{{ route('admin.products.edit', $product) }}" class="font-medium text-blue-600 hover:underline text-lg dark:text-blue-400">
                                                        {{ $product->name }}
                                                        <span class="text-xs text-gray-400 mr-1">↗</span>
                                                    </a>
                                                @else
                                                    <span class="font-medium text-gray-500 dark:text-gray-400">{{ $item->product_name }} (محصول حذف شده)</span>
                                                @endif
                                                
                                                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                    @if ($variant)
                                                        @if($variant->size) <span>سایز: {{ $variant->size }}</span> @endif
                                                        @if($variant->size && $variant->color) <span class="mx-1">|</span> @endif
                                                        @if($variant->color) 
                                                            <span>رنگ: {{ $variant->color }}</span> 
                                                        @endif
                                                    @else
                                                        <span class="text-red-400">(این متغیر ناموجود است)</span>
                                                    @endif
                                                </div>
                                                <div class="text-xs text-gray-400 mt-0.5">
                                                    کد محصول: {{ $product->product_id ?? '---' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 align-middle dark:text-gray-200">
                                        {{ number_format($item->price) }} <span class="text-xs">تومان</span>
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded border border-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600">
                                            x {{ $item->quantity }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 align-middle text-left font-bold text-gray-700 dark:text-white">
                                        {{ number_format($item->price * $item->quantity) }} <span class="text-xs font-normal">تومان</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white dark:bg-dark-paper shadow-md rounded-lg transition-colors">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold dark:text-white">اطلاعات ارسال</h2>
                </div>
                @if ($order->address)
                    <div class="p-6 text-gray-700 dark:text-gray-300 space-y-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">تحویل گیرنده:</p>
                                <p class="font-medium">{{ $order->address->full_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">شماره تماس:</p>
                                <p class="font-medium" dir="rtl">{{ $order->address->phone }}</p>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-400">آدرس کامل:</p>
                            <p>{{ $order->address->state }}، {{ $order->address->city }}</p>
                            <p>{{ $order->address->address }}</p>
                            <p>کد پستی: <span dir="ltr">{{ $order->address->zip_code }}</span></p>
                        </div>
                    </div>
                @else
                    <p class="p-6 text-gray-500 dark:text-gray-400">آدرس ارسالی برای این سفارش ثبت نشده است.</p>
                @endif
            </div>
        </div>

        <div class="lg:col-span-1 space-y-8">
            <div class="bg-white dark:bg-dark-paper shadow-md rounded-lg transition-colors">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold dark:text-white">خلاصه مالی</h2>
                </div>
                <div class="p-6 space-y-4">
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between items-center">
                            <dt class="text-gray-600 dark:text-gray-400">خریدار</dt>
                            <dd class="font-medium truncate max-w-[150px] dark:text-gray-200" title="{{ $order->user ? $order->user->name : 'مهمان' }}">
                                {{ $order->user ? $order->user->name : ($order->address->full_name ?? 'کاربر مهمان') }}
                            </dd>
                        </div>
                        
                        <div class="flex justify-between items-center p-2 bg-gray-50 dark:bg-dark-hover rounded transition-colors">
                            <dt class="text-gray-600 dark:text-gray-400">روش پرداخت</dt>
                            <dd class="font-medium dark:text-gray-200">
                                @if($order->payment_method == 'cod') پرداخت در محل
                                @elseif($order->payment_method == 'online') آنلاین
                                @elseif($order->payment_method == 'card') کارت به کارت
                                @else {{ $order->payment_method }} @endif
                            </dd>
                        </div>
                        @if($order->transaction_code)
                        <div class="flex justify-between items-center">
                            <dt class="text-gray-600 dark:text-gray-400">کد تراکنش</dt>
                            <dd class="font-mono text-xs bg-gray-100 dark:bg-gray-700 dark:text-gray-200 px-2 py-1 rounded">{{ $order->transaction_code }}</dd>
                        </div>
                        @endif

                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-2"></div>

                        <div class="flex justify-between items-center">
                            <dt class="text-gray-600 dark:text-gray-400">جمع سبد خرید</dt>
                            <dd class="font-medium dark:text-gray-200">{{ number_format($order->subtotal) }}</dd>
                        </div>
                        <div class="flex justify-between items-center">
                            <dt class="text-gray-600 dark:text-gray-400">هزینه ارسال ({{ $order->shipping_method == 'pishaz' ? 'پیشتاز' : 'تیپاکس' }})</dt>
                            <dd class="font-medium dark:text-gray-200">{{ number_format($order->shipping_cost) }}</dd>
                        </div>
                        <div class="flex justify-between items-center">
                            <dt class="text-gray-600 dark:text-gray-400">بسته‌بندی ({{ $order->packagingOption->name ?? 'استاندارد' }})</dt>
                            <dd class="font-medium dark:text-gray-200">{{ number_format($order->packaging_cost) }}</dd>
                        </div>
                        
                        @if ($order->discount_amount > 0)
                        <div class="flex justify-between items-center text-red-600 bg-red-50 dark:bg-red-900/20 dark:text-red-400 p-2 rounded">
                            <dt>تخفیف ({{ $order->discount_code }})</dt>
                            <dd class="font-medium">-{{ number_format($order->discount_amount) }}</dd>
                        </div>
                        @endif
                        
                        <div class="flex justify-between items-center text-lg font-bold border-t border-gray-200 dark:border-gray-700 pt-4 text-gray-900 dark:text-white">
                            <dt>مبلغ کل</dt>
                            <dd>{{ number_format($order->total) }} تومان</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="bg-white dark:bg-dark-paper shadow-md rounded-lg transition-colors">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-dark-hover">
                    <h2 class="text-base font-semibold text-gray-700 dark:text-gray-200">مدیریت وضعیت سفارش</h2>
                </div>
                <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">وضعیت فعلی</label>
                        <select id="status" name="status" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-dark-bg dark:border-gray-600 dark:text-gray-200">
                            <option value="pending" @selected($order->status == 'pending')>در انتظار تایید</option>
                            <option value="processing" @selected($order->status == 'processing')>در حال پردازش</option>
                            <option value="shipped" @selected($order->status == 'shipped')>ارسال شده</option>
                            <option value="completed" @selected($order->status == 'completed')>تحویل شده</option>
                            <option value="cancelled" @selected($order->status == 'cancelled')>لغو شده</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:hover:bg-blue-500 transition-colors">
                        ذخیره تغییرات
                    </button>
                </form>
            </div>
        </div>
        
    </div>
</div>
@endsection