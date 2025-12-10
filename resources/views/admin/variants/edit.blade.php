@extends('admin.layouts.app')

@section('title', 'ویرایش مدل')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-right dark:text-white">ویرایش مدل</h1>
        <a href="{{ route('admin.products.edit', $product) }}" class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-dark-hover transition-colors">
            بازگشت به محصول &rarr;
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 dark:bg-red-900/50 dark:text-red-300 dark:border-red-600 px-4 py-3 rounded relative mb-4 text-right" role="alert">
            <strong class="font-bold">خطا!</strong>
            <span class="block sm:inline">ورودی شما دارای چند مشکل است.</span>
            <ul class="mt-3 list-disc list-inside text-sm text-right">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white dark:bg-dark-paper shadow-md rounded-lg p-6 transition-colors">
        <form action="{{ route('admin.variants.update', $variant) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="variant_size" class="block text-sm font-medium text-gray-700 dark:text-gray-300">سایز</label>
                    <select name="size" id="variant_size"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-dark-bg dark:border-gray-600 dark:text-white transition-colors" required>
                        <option value="">انتخاب کنید...</option>
                        @foreach ($sizes as $size)
                            <option value="{{ $size->name }}" @selected(old('size', $variant->size) == $size->name)>
                                {{ $size->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-right">رنگ</label>
                    <select name="color" id="color"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-dark-bg dark:border-gray-600 dark:text-white transition-colors">
                        <option value="">انتخاب کنید...</option>
                        @foreach ($colors as $color)
                            <option value="{{ $color->name }}" @selected(old('color', $variant->color) == $color->name)>
                                {{ $color->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-right">قیمت (به تومان)</label>
                    <input type="number" name="price" id="price" value="{{ old('price', $variant->price) }}" 
                           step="1" min="0" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right dark:bg-dark-bg dark:border-gray-600 dark:text-white transition-colors" required>
                </div>
                <div>
                    <label for="discount_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">قیمت با تخفیف (اختیاری)</label>
                    <input type="number" name="discount_price" id="discount_price" value="{{ old('discount_price', $variant->discount_price) }}" 
                           step="1" min="0" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-dark-bg dark:border-gray-600 dark:text-white dark:placeholder-gray-500 transition-colors" placeholder="مثلا: 45000">
                </div>
                <div>
                    <label for="buy_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">قیمت خرید</label>
                    <input type="number" name="buy_price" id="buy_price" value="{{ old('buy_price', $variant->buy_price) }}" 
                           step="1" min="0" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-dark-bg dark:border-gray-600 dark:text-white dark:placeholder-gray-500 transition-colors" placeholder="مثلا: 45000">
                </div>
                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-right">تعداد موجودی</label>
                    <input type="number" name="stock" id="stock" value="{{ old('stock', $variant->stock) }}" 
                           min="0" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right dark:bg-dark-bg dark:border-gray-600 dark:text-white transition-colors" required>
                </div>
                <div>
                    <label for="buy_source" class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-right">سورس خرید</label>
                    <select name="buy_source_id" id="buy_source_id"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-dark-bg dark:border-gray-600 dark:text-white transition-colors">
                        <option value="">انتخاب کنید...</option>
                        @foreach ($buySources as $source)
                            <option value="{{ $source->id }}" @selected(old('buy_source_id', $variant->buy_source_id) == $source->id)>
                                {{ $source->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-start mt-6">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    ذخیره تغییرات
                </button>
            </div>
        </form>
    </div>
@endsection