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

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="md:col-span-2 space-y-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-right">نام محصول</label>
            <input type="text" name="name" id="name" value="{{ old('name', $product->name ?? '') }}" 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right dark:bg-dark-paper dark:border-gray-600 dark:text-white transition-colors" required>
        </div>

        <div>
            <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">اسلاگ (URL)</label>
            <input type="text" 
                   name="slug" 
                   id="slug" 
                   value="{{ old('slug', $product->slug ?? '') }}" 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-600 dark:bg-dark-paper dark:border-gray-600 dark:text-gray-300 transition-colors" 
                   placeholder="auto-generated-slug"
                   dir="ltr">
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">در صورت خالی گذاشتن، به طور خودکار از روی نام محصول ساخته می‌شود.</p>
        </div>
        
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-right">توضیحات</label>
            <textarea name="description" id="description" rows="8" 
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right dark:bg-dark-paper dark:border-gray-600 dark:text-white transition-colors">{{ old('description', $product->description ?? '') }}</textarea>
        </div>

        <div>
            <label for="care_and_maintenance" class="block text-sm font-medium text-gray-700 dark:text-gray-300">مراقبت و نگهداری (اختیاری)</label>
            <textarea name="care_and_maintenance" id="care_and_maintenance" rows="5" 
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-dark-paper dark:border-gray-600 dark:text-white transition-colors"
                      placeholder="مثال: شستشو فقط با آب سرد...">{{ old('care_and_maintenance', $product->care_and_maintenance ?? '') }}</textarea>
        </div>
    </div>

    <div class="md:col-span-1 space-y-6">
        <div>
            <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-right">دسته بندی</label>
            <select name="category_id" id="category_id" 
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right dark:bg-dark-paper dark:border-gray-600 dark:text-white transition-colors" required>
                <option value="">یک دسته بندی انتخاب کنید</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" 
                        @isset($product) {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }} @endisset>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="bg-white shadow rounded-lg p-4 dark:bg-dark-paper transition-colors">
            <label for="product_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">شناسه محصول (SKU)</label>
            <input type="text" name="product_id" id="product_id" value="{{ old('product_id', $product->product_id ?? '') }}" 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-500 dark:bg-dark-bg dark:border-gray-600 dark:text-gray-300 transition-colors" 
                   dir="ltr">
        </div>

        <div>
            <label for="invoice_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">شماره فاکتور (اختیاری)</label>
            <input type="text" 
                name="invoice_number" 
                id="invoice_number" 
                value="{{ old('invoice_number', $product->invoice_number ?? '') }}"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm dark:bg-dark-paper dark:border-gray-600 dark:text-white transition-colors"
                placeholder="مثال: F-1403-25">
        </div>

        <div class="md:col-span-1 space-y-6">
            <div class="bg-white shadow rounded-lg p-4 dark:bg-dark-paper transition-colors">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">جنسیت</label>
                <div class="space-y-2 mt-3">
                    <div class="flex items-center">
                        <input type="hidden" name="is_for_men" value="0">
                        <input id="is_for_men" name="is_for_men" type="checkbox" value="1" 
                               @checked(old('is_for_men', $product->is_for_men ?? false))
                               class="h-4 w-4 text-blue-600 border-gray-300 rounded ml-2 dark:bg-dark-bg dark:border-gray-600 dark:checked:bg-blue-600 transition-colors">
                        <label for="is_for_men" class="text-sm text-gray-900 dark:text-gray-300">برای آقایان</label>
                    </div>
                    <div class="flex items-center">
                        <input type="hidden" name="is_for_women" value="0">
                        <input id="is_for_women" name="is_for_women" type="checkbox" value="1" 
                               @checked(old('is_for_women', $product->is_for_women ?? false))
                               class="h-4 w-4 text-blue-600 border-gray-300 rounded ml-2 dark:bg-dark-bg dark:border-gray-600 dark:checked:bg-blue-600 transition-colors">
                        <label for="is_for_women" class="text-sm text-gray-900 dark:text-gray-300">برای بانوان</label>
                    </div>
                </div>
            </div>
            
            <div class="bg-white shadow rounded-lg p-4 flex items-center dark:bg-dark-paper transition-colors">
                <input type="hidden" name="is_visible" value="0">
                <input type="checkbox" name="is_visible" id="is_visible" value="1" 
                       @checked(old('is_visible', $product->is_visible ?? true))
                       class="h-4 w-4 text-blue-600 border-gray-300 rounded ml-2 dark:bg-dark-bg dark:border-gray-600 dark:checked:bg-blue-600 transition-colors">
                <label for="is_visible" class="ml-2 block text-sm font-medium text-gray-900 dark:text-gray-300">قابل مشاهده در فروشگاه</label>
            </div>
        </div>
    </div>
</div>