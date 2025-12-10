@extends('admin.layouts.app')

@section('title', 'مدیریت محصولات')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div dir="rtl">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold dark:text-white">محصولات</h1>
            
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                @if($selectedCategory)
                    <span>
                        نمایش <span class="font-bold text-blue-600 dark:text-blue-400">{{ number_format($productCount) }}</span> محصول در دسته‌بندی: <span class="font-bold text-blue-600 dark:text-blue-400">{{ $selectedCategory->name }}</span>
                    </span>
                    <a href="{{ route('admin.products.index') }}" class="text-xs text-red-500 hover:underline dark:text-red-400">[حذف فیلتر]</a>
                @else
                    <span>
                        نمایش کل محصولات: <span class="font-bold text-gray-800 dark:text-gray-200">{{ number_format($productCount) }}</span> عدد
                    </span>
                @endif
            </p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            + ایجاد محصول
        </a>
    </div>

    <div class="mb-6" 
         x-data="{
             isOpen: false,
             searchQuery: '',
             // لیست کامل دسته‌بندی‌ها از کنترلر می‌آید
             allCategories: {{ $categories->toJson() }},
             
             // فیلتر کردن لیست بر اساس جستجو
             get filteredCategories() {
                 if (this.searchQuery === '') {
                     return this.allCategories;
                 }
                 return this.allCategories.filter(category => 
                     category.name.toLowerCase().includes(this.searchQuery.toLowerCase())
                 );
             },
             
             // اعمال فیلتر با ریدایرکت کردن صفحه
             selectCategory(categoryId) {
                 let url = new URL(window.location.href);
                 url.searchParams.set('category_id', categoryId);
                 url.searchParams.delete('page'); // بازگشت به صفحه ۱
                 window.location.href = url.href;
             }
         }">
        
        <div class="relative">
            <label for="category_search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">فیلتر بر اساس دسته‌بندی</label>
            <input type="text"
                   id="category_search"
                   x-model="searchQuery"
                   @focus="isOpen = true"
                   @click.away="isOpen = false"
                   placeholder="جستجو یا انتخاب دسته‌بندی..."
                   autocomplete="off"
                   class="mt-1 block w-full md:w-1/3 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-dark-bg dark:border-gray-600 dark:text-white dark:placeholder-gray-500 transition-colors">
            
            <div x-show="isOpen" 
                 x-transition
                 class="absolute z-10 mt-1 w-full md:w-1/3 bg-white dark:bg-dark-paper shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm border dark:border-gray-600"
                 style="display: none;">
                
                <ul class="py-1">
                    <template x-for="category in filteredCategories" :key="category.id">
                        <li @click="selectCategory(category.id)"
                            class="text-gray-900 dark:text-gray-200 cursor-pointer select-none relative py-2 px-4 hover:bg-gray-100 dark:hover:bg-dark-hover">
                            <span x-text="category.name"></span>
                        </li>
                    </template>
                    <li x-show="filteredCategories.length === 0 && searchQuery !== ''" class="py-2 px-4 text-gray-500 dark:text-gray-400">
                        دسته‌بندی با این نام یافت نشد.
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    @if(session('success'))
        <div class="bg-green-100 border-r-4 border-green-500 text-green-700 dark:bg-green-900/50 dark:text-green-300 dark:border-green-600 p-4 mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-dark-paper shadow-md rounded-lg overflow-hidden transition-colors">
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead class="bg-gray-100 dark:bg-dark-hover">
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">نام</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">ایجادکننده</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">دسته‌بندی</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">جنسیت</th>
                         <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                        شناسه محصول
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">وضعیت</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-dark-hover text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider"></th>
                    </tr>
                </thead>
                <tbody class="dark:text-gray-200">
                    @forelse ($products as $product)
                        <tr class="hover:bg-gray-50 dark:hover:bg-dark-hover transition-colors">
                            
                            <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                                <p class="text-gray-900 dark:text-white whitespace-no-wrap">{{ $product->name }}</p>
                            </td>
                            
                            <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                                <p class="text-gray-600 dark:text-gray-400 whitespace-no-wrap">
                                    {{ $product->admin?->name ?? 'نامشخص' }}
                                </p>
                            </td>
                            
                            <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                                <p class="text-gray-900 dark:text-white whitespace-no-wrap">{{ $product->category->name ?? 'N/A' }}</p>
                            </td>
                            
                            <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                                @if($product->is_for_men && $product->is_for_women)
                                    <span class="font-semibold text-gray-800 dark:text-gray-200">هردو</span>
                                @elseif($product->is_for_men)
                                    <span class="font-semibold text-blue-600 dark:text-blue-400">آقایان</span>
                                @elseif($product->is_for_women)
                                    <span class="font-semibold text-pink-600 dark:text-pink-400">بانوان</span>
                                @else
                                    <span class="text-gray-400">---</span>
                                @endif
                            </td>
                            
                            <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                                <p class="text-gray-900 dark:text-white whitespace-no-wrap">{{ $product->product_id }}</p>
                            </td>
                            
                            <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                                @if($product->is_visible)
                                    <span class="relative inline-block px-3 py-1 font-semibold text-green-900 dark:text-green-200 leading-tight">
                                        <span aria-hidden class="absolute inset-0 bg-green-200 dark:bg-green-900 opacity-50 rounded-full"></span>
                                        <span class="relative">قابل مشاهده</span>
                                    </span>
                                @else
                                    <span class="relative inline-block px-3 py-1 font-semibold text-gray-700 dark:text-gray-300 leading-tight">
                                        <span aria-hidden class="absolute inset-0 bg-gray-200 dark:bg-gray-700 opacity-50 rounded-full"></span>
                                        <span class="relative">مخفی</span>
                                    </span>
                                @endif
                            </td>
                            
                            <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 text-left whitespace-nowrap">
                                <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 ml-4 transition-colors">ویرایش</a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline-block" onsubmit="return confirm('آیا از حذف این محصول مطمئن هستید؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-dark-paper text-sm text-center text-gray-500 dark:text-gray-400">
                                @if($selectedCategory)
                                    هیچ محصولی در این دسته‌بندی یافت نشد.
                                @else
                                    هیچ محصولی یافت نشد. <a href="{{ route('admin.products.create') }}" class="text-blue-600 dark:text-blue-400 hover:underline">یکی ایجاد کنید!</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    
        <div class="p-4">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection