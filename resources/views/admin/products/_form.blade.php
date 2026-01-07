@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 text-right">
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Alpine Component --}}
<div x-data="{ 
    tab: 'general',
    publishType: '{{ $product->type_internal ? 'internal' : ($product->type_appstore ? 'appstore' : 'pwa_adhoc') }}',
    appId: '',
    loadingFetch: false,
    fetchedIcon: '',
    fetchedScreenshots: [],
    translationError: '',
    
    formatPrice(v) { return v ? v.toString().replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',') : ''; },

    fetchFromItunes() {
        if (!this.appId) { alert('لطفا Apple ID برنامه را وارد کنید'); return; }
        
        this.loadingFetch = true;
        
        fetch('{{ route('admin.products.fetch-itunes') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ id: this.appId })
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(res => {
            this.loadingFetch = false;
            if (!res.success) {
                alert(res.message);
                return;
            }
            
            let data = res.data;
            
            // پر کردن فیلدها با استفاده از ID
            // این روش از تداخل x-model جلوگیری می‌کند
            const setVal = (name, val) => {
                let el = document.querySelector(`[name='${name}']`);
                if(el) el.value = val || '';
            };

            setVal('title', data.title);
            setVal('name_en', data.name_en);
            setVal('bundle_id', data.bundle_id);
            setVal('version', data.version);
            setVal('size', data.size);
            setVal('seller', data.seller);
            setVal('seller_website', data.seller_website);

            
            setVal('description', data.description);
            setVal('description_fa', data.description_fa);

            setVal('release_notes', data.release_notes);
            setVal('release_notes_fa', data.release_notes_fa);

            if (data.translation_error) {
                this.translationError = '⚠️ ' + data.translation_error;
            } else {
                this.translationError = ''; // پاک کردن خطا در صورت موفقیت
            }

            setVal('appstore_link', data.appstore_link);
            setVal('native_appstore_url', data.appstore_link); // پر کردن لینک اپ استور در بخش انتشار
            

            // قیمت
            let priceInput = document.querySelector(`[name='price_appstore_display']`);
            if(priceInput) {
                priceInput.value = this.formatPrice(data.price_appstore);
                // تریگر کردن رویداد برای آپدیت شدن اینپوت مخفی
                priceInput.dispatchEvent(new Event('input'));
            }

            // دسته‌بندی
            if (data.category_id) {
                setVal('category_id', data.category_id);
            }

            // عکس‌ها
            this.fetchedIcon = data.icon_url;
            this.fetchedScreenshots = data.screenshots_urls;

            alert('اطلاعات دریافت شد! فرم را بررسی کنید.');
        })
        .catch(err => {
            console.error(err);
            this.loadingFetch = false;
            alert('خطا در دریافت اطلاعات. (لطفا کنسول مرورگر را چک کنید)');
        });
    }
}">
    
    {{-- API Fetch Block --}}
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-8">
        <div class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <label class="block text-sm font-bold text-blue-800 dark:text-blue-300 mb-2">دریافت اطلاعات از AppStore</label>
                <div class="flex gap-2">
                    <input type="text" x-model="appId" class="flex-1 px-4 py-2 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white ltr placeholder-gray-400" placeholder="Apple ID (e.g. 123456789)">
                    <button type="button" @click="fetchFromItunes()" :disabled="loadingFetch" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors">
                        <span x-show="!loadingFetch">دریافت اطلاعات</span>
                        <span x-show="loadingFetch">در حال دریافت...</span>
                    </button>
                </div>
                <p class="text-xs text-blue-600 dark:text-blue-400 mt-2">با وارد کردن ID، نام، آیکون، اسکرین‌شات‌ها و ... به صورت خودکار پر می‌شوند.</p>
            </div>
        </div>
    </div>


    {{-- Tabs Header --}}
    <div class="flex border-b border-gray-200 dark:border-gray-700 mb-6">
        <button type="button" @click="tab = 'general'" :class="{ 'border-b-2 border-blue-500 text-blue-600': tab === 'general' }" class="px-6 py-3 font-medium text-gray-600 dark:text-gray-300">اطلاعات پایه</button>
        <button type="button" @click="tab = 'visual'" :class="{ 'border-b-2 border-blue-500 text-blue-600': tab === 'visual' }" class="px-6 py-3 font-medium text-gray-600 dark:text-gray-300">محتوای بصری</button>
        <button type="button" @click="tab = 'publish'" :class="{ 'border-b-2 border-blue-500 text-blue-600': tab === 'publish' }" class="px-6 py-3 font-medium text-gray-600 dark:text-gray-300">انتشار و فایل‌ها</button>
    </div>

    {{-- TAB 1: General Info --}}
    <div x-show="tab === 'general'" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Title --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">عنوان محصول</label>
                <input type="text" name="title" value="{{ old('title', $product->title ?? '') }}" class="w-full px-4 py-2 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white" required>
            </div>
            {{-- Persian Name --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">نام فارسی</label>
                <input type="text" name="name_fa" value="{{ old('name_fa', $product->name_fa ?? '') }}" class="w-full px-4 py-2 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white">
            </div>
            {{-- English Name --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">نام انگلیسی</label>
                <input type="text" name="name_en" value="{{ old('name_en', $product->name_en ?? '') }}" class="w-full px-4 py-2 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white">
            </div>
             {{-- Category --}}
             <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">دسته‌بندی</label>
                <select name="category_id" class="w-full px-4 py-2 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white">
                    <option value="">انتخاب کنید...</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected($cat->id == ($product->category_id ?? 0))>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <hr class="dark:border-gray-700">

        <div class="grid grid-cols-1 gap-6">

        {{-- Subscription Only Checkbox --}}
        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 rounded-lg">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" name="is_subscription_only" value="1" 
                    class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                    @checked(old('is_subscription_only', $product->is_subscription_only ?? false))>
                
                <div class="mr-3">
                    <span class="block text-sm font-bold text-gray-800 dark:text-white">رایگان (مخصوص مشترکین)</span>
                    <span class="block text-xs text-gray-500 dark:text-gray-400">
                        اگر تیک بزنید، قیمت‌ها نادیده گرفته می‌شوند و اپلیکیشن فقط برای مشترکین انتخابی (در پایین) رایگان خواهد بود. خرید تکی غیرفعال می‌شود.
                    </span>
                </div>
            </label>
        </div>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
             {{-- Prices --}}

            <div x-data="{ 
                val: '{{ old('price_appstore', $product->price_appstore ?? '') }}',
                format(v) { return v ? v.toString().replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',') : ''; }
             }">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">قیمت دلاری</label>
                <input type="text" name="price_appstore_display" class="w-full px-4 py-2 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white ltr font-mono"
                       :value="format(val)" @input="val = $event.target.value.replace(/[^0-9]/g, ''); $event.target.value = format(val)" placeholder="(نمایشی)">
                <input type="hidden" name="price_appstore" :value="val">
            </div>

            <div x-data="{ 
                val: '{{ old('price', $product->price ?? '') }}',
                format(v) { return v ? v.toString().replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',') : ''; }
             }">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">قیمت بدون اشتراک</label>
                <input type="text" name="price_display" class="w-full px-4 py-2 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white ltr font-mono"
                       :value="format(val)" @input="val = $event.target.value.replace(/[^0-9]/g, ''); $event.target.value = format(val)" placeholder="تومان">
                <input type="hidden" name="price" :value="val">
            </div>

             <div x-data="{ 
                val: '{{ old('price_sibaneh', $product->price_sibaneh ?? '') }}',
                format(v) { return v ? v.toString().replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',') : ''; }
             }">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">قیمت در سیبانه</label>
                <input type="text" class="w-full px-4 py-2 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white ltr font-mono"
                       :value="format(val)" @input="val = $event.target.value.replace(/[^0-9]/g, ''); $event.target.value = format(val)" placeholder="تومان">
                <input type="hidden" name="price_sibaneh" :value="val">
            </div>


            <div x-data="{ 
                val: '{{ old('price_sibaneh_plus', $product->price_sibaneh_plus ?? '') }}',
                format(v) { return v ? v.toString().replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',') : ''; }
             }">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">قیمت سیبانه پلاس</label>
                <input type="text" name="price_sibaneh_plus_display" class="w-full px-4 py-2 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white ltr font-mono"
                       :value="format(val)" @input="val = $event.target.value.replace(/[^0-9]/g, ''); $event.target.value = format(val)" placeholder="تومان">
                <input type="hidden" name="price_sibaneh_plus" :value="val">
            </div>

            <div x-data="{ 
                val: '{{ old('price_sibaneh_pro', $product->price_sibaneh_pro ?? '') }}',
                format(v) { return v ? v.toString().replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',') : ''; }
             }">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">قیمت سیبانه پرو</label>
                <input type="text" name="price_sibaneh_pro_display" class="w-full px-4 py-2 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white ltr font-mono"
                       :value="format(val)" @input="val = $event.target.value.replace(/[^0-9]/g, ''); $event.target.value = format(val)" placeholder="تومان">
                <input type="hidden" name="price_sibaneh_pro" :value="val">
            </div>

            <div x-data="{ 
                val: '{{ old('price_arcade', $product->price_arcade ?? '') }}',
                format(v) { return v ? v.toString().replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',') : ''; }
             }">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">قیمت آرکید</label>
                <input type="text" name="price_arcade_display" class="w-full px-4 py-2 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white ltr font-mono"
                       :value="format(val)" @input="val = $event.target.value.replace(/[^0-9]/g, ''); $event.target.value = format(val)" placeholder="تومان">
                <input type="hidden" name="price_arcade" :value="val">
            </div>

            {{-- Version --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">نسخه (Version)</label>
                <input type="text" name="version" value="{{ old('version', $product->version ?? '1.0.0') }}" class="w-full px-4 py-2 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white ltr">
            </div>
        </div>

        <hr class="dark:border-gray-700">

        <div class="grid grid-cols-1 gap-6">
            
            {{-- بخش انتخاب لایسنس (اشتراک یا ادآن) --}}
            <div x-data="{ 
                hasSub: {{ (is_array(old('subscriptions')) && count(old('subscriptions')) > 0) || (isset($product) && $product->subscriptions->count() > 0) ? 'true' : 'false' }},
                hasAddon: {{ (is_array(old('addons')) && count(old('addons')) > 0) || (isset($product) && $product->addons->count() > 0) ? 'true' : 'false' }}
            }" class="bg-white dark:bg-dark-paper p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2 border-b dark:border-gray-600 pb-3">تنظیمات دسترسی و دانلود</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">مشخص کنید این اپلیکیشن با کدام سرویس‌ها قابل دانلود است. (فقط یکی از گروه‌های زیر قابل انتخاب است)</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    {{-- 1. لیست اشتراک‌ها --}}
                    <div class="p-4 rounded-xl border-2 transition-all duration-200"
                        :class="hasAddon ? 'border-gray-200 bg-gray-50 opacity-50 cursor-not-allowed' : 'border-blue-500/30 bg-blue-50/10'">
                        
                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-blue-600 dark:text-blue-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                            </span>
                            <label class="font-bold text-gray-700 dark:text-gray-200">مجاز برای اشتراک‌های:</label>
                        </div>

                        <div class="space-y-3 max-h-48 overflow-y-auto px-1">
                            @forelse($subscriptions as $sub)
                                <label class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-white dark:hover:bg-gray-700 cursor-pointer transition-colors">
                                    <input type="checkbox" name="subscriptions[]" value="{{ $sub->id }}"
                                        class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500 disabled:opacity-50"
                                        :disabled="hasAddon"
                                        @change="hasSub = Array.from(document.querySelectorAll('input[name=\'subscriptions[]\']:checked')).length > 0"
                                        @checked((is_array(old('subscriptions')) && in_array($sub->id, old('subscriptions'))) || (isset($product) && $product->subscriptions->contains($sub->id)))>
                                    
                                    <div class="mr-3 flex flex-col">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $sub->name }}</span>
                                        <!-- <span class="text-xs text-gray-500">{{ $sub->slug }}</span> -->
                                    </div>
                                </label>
                            @empty
                                <p class="text-sm text-red-500 text-center py-4">هیچ اشتراکی یافت نشد.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- 2. لیست افزودنی‌ها --}}
                    <div class="p-4 rounded-xl border-2 transition-all duration-200"
                        :class="hasSub ? 'border-gray-200 bg-gray-50 opacity-50 cursor-not-allowed' : 'border-purple-500/30 bg-purple-50/10'">
                        
                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-purple-600 dark:text-purple-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </span>
                            <label class="font-bold text-gray-700 dark:text-gray-200">مجاز برای خریداران افزودنی:</label>
                        </div>

                        <div class="space-y-3 max-h-48 overflow-y-auto px-1">
                            @forelse($addons as $addon)
                                <label class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-white dark:hover:bg-gray-700 cursor-pointer transition-colors">
                                    <input type="checkbox" name="addons[]" value="{{ $addon->id }}"
                                        class="w-5 h-5 text-purple-600 rounded border-gray-300 focus:ring-purple-500 disabled:opacity-50"
                                        :disabled="hasSub"
                                        @change="hasAddon = Array.from(document.querySelectorAll('input[name=\'addons[]\']:checked')).length > 0"
                                        @checked((is_array(old('addons')) && in_array($addon->id, old('addons'))) || (isset($product) && $product->addons->contains($addon->id)))>
                                    
                                    <div class="mr-3 flex flex-col">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $addon->name }}</span>
                                        <!-- <span class="text-xs text-gray-500">{{ number_format($addon->price) }} تومان</span> -->
                                    </div>
                                </label>
                            @empty
                                <div class="flex flex-col items-center justify-center py-6 text-gray-400 border-2 border-dashed border-gray-300 rounded-lg">
                                    <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                    <span class="text-sm">هیچ افزودنی تعریف نشده است</span>
                                    <a href="#" class="text-xs text-blue-500 mt-2 hover:underline">ایجاد افزودنی جدید</a>
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>
    
                {{-- نمایش ارورها --}}
                @if($errors->has('subscriptions') || $errors->has('addons'))
                    <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded text-red-600 text-sm">
                        {{ $errors->first('subscriptions') ?: $errors->first('addons') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Bundle ID --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bundle ID</label>
                <input type="text" name="bundle_id" value="{{ old('bundle_id', $product->bundle_id ?? '') }}" class="w-full px-4 py-2 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white ltr">
            </div>
            {{-- Size --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">حجم (Size)</label>
                <input type="text" name="size" value="{{ old('size', $product->size ?? '') }}" class="w-full px-4 py-2 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white ltr" placeholder="e.g. 150 MB">
            </div>
            {{-- Seller --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">فروشنده / توسعه‌دهنده</label>
                <input type="text" name="seller" value="{{ old('seller', $product->seller ?? '') }}" class="w-full px-4 py-2 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white">
            </div>
            {{-- Website --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">وبسایت فروشنده</label>
                <input type="text" name="seller_website" value="{{ old('seller_website', $product->seller_website ?? '') }}" class="w-full px-4 py-2 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white ltr">
            </div>
        </div>
        
        <div class="flex items-center gap-6">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" name="is_stable" value="1" @checked(old('is_stable', $product->is_stable ?? true)) class="w-5 h-5 text-green-600 rounded">
                <span class="mr-2 text-sm font-bold text-gray-700 dark:text-gray-300">نسخه پایدار (Stable)</span>
            </label>
            <div class="flex items-center gap-4">
                <label class="flex items-center">
                    <input type="radio" name="availability" value="available" @checked(($product->availability ?? 'available') == 'available') class="text-blue-600">
                    <span class="mr-1 text-sm dark:text-gray-300">موجود</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="availability" value="not_available" @checked(($product->availability ?? '') == 'not_available') class="text-red-600">
                    <span class="mr-1 text-sm dark:text-gray-300">ناموجود</span>
                </label>
            </div>
        </div>

        {{-- Description English --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">توضیحات اصلی (انگلیسی)</label>
            <textarea name="description" rows="4" class="w-full px-4 py-2 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white ltr">{{ old('description', $product->description ?? '') }}</textarea>
        </div>

        {{-- Description Persian (NEW) --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">توضیحات فارسی (ترجمه هوشمند)</label>
            <textarea name="description_fa" rows="4" class="w-full px-4 py-2 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white" placeholder="پس از دریافت اطلاعات، ترجمه اینجا قرار می‌گیرد...">{{ old('description_fa', $product->description_fa ?? '') }}</textarea>
        
            <p x-show="translationError" x-text="translationError" class="text-red-500 text-xs mt-2 font-bold"></p>
        </div>

        <hr class="dark:border-gray-700 my-6">

        {{-- Release Notes English --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">تغییرات نسخه (انگلیسی)</label>
            <textarea name="release_notes" rows="3" class="w-full px-4 py-2 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white ltr">{{ old('release_notes', $product->release_notes ?? '') }}</textarea>
        </div>

        {{-- Release Notes Persian --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">تغییرات نسخه (فارسی)</label>
            <textarea name="release_notes_fa" rows="3" class="w-full px-4 py-2 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white" placeholder="ترجمه خودکار تغییرات...">{{ old('release_notes_fa', $product->release_notes_fa ?? '') }}</textarea>
        </div>
    </div>

    {{-- TAB 2: Visual Metadata --}}
    <div x-show="tab === 'visual'" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Icon --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">آیکون برنامه</label>
                <input type="file" name="icon" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                
                {{-- Show Fetched Icon --}}
                <template x-if="fetchedIcon">
                    <div class="mt-2">
                        <p class="text-xs text-green-600 mb-1">آیکون دریافت شده:</p>
                        <img :src="fetchedIcon" class="w-16 h-16 rounded-xl shadow border-2 border-green-500">
                        <input type="hidden" name="fetched_icon_url" :value="fetchedIcon">
                    </div>
                </template>

                @if($product->icon_path)
                    <img src="{{ asset('storage/'.$product->icon_path) }}" class="w-16 h-16 mt-2 rounded-xl shadow">
                @endif
            </div>

            {{-- Banner Detail --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">بنر صفحه جزئیات</label>
                <input type="file" name="banner_detail" class="block w-full text-sm text-gray-500">
                @if($product->banner_detail_path)
                    <img src="{{ asset('storage/'.$product->banner_detail_path) }}" class="h-16 mt-2 rounded shadow">
                @endif
            </div>

            {{-- Banner Vitrin --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">بنر ویترین</label>
                <input type="file" name="banner_vitrin" class="block w-full text-sm text-gray-500">
                @if($product->banner_vitrin_path)
                    <img src="{{ asset('storage/'.$product->banner_vitrin_path) }}" class="h-16 mt-2 rounded shadow">
                @endif
            </div>

            {{-- Intro Video --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">لینک ویدیو معرفی</label>
                <input type="text" name="video_url" value="{{ old('video_url', $product->video_url ?? '') }}" class="w-full px-4 py-2 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white ltr" placeholder="http://...">
            </div>
        </div>

        {{-- Screenshots --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">اسکرین‌شات‌ها</label>
            <input type="file" name="screenshots[]" multiple class="block w-full text-sm text-gray-500">
            
            {{-- Show Fetched Screenshots --}}
            <template x-if="fetchedScreenshots.length > 0">
                <div class="mt-4">
                    <p class="text-xs text-green-600 mb-2">اسکرین‌شات‌های دریافت شده (خودکار ذخیره می‌شوند):</p>
                    <div class="flex gap-2 overflow-x-auto pb-2">
                        <template x-for="url in fetchedScreenshots">
                            <div class="relative flex-shrink-0">
                                <img :src="url" class="h-24 rounded shadow border border-green-300">
                                <input type="hidden" name="fetched_screenshots[]" :value="url">
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            @if(isset($product) && $product->screenshots->count())
                <div class="flex gap-2 mt-4 overflow-x-auto">
                    @foreach($product->screenshots as $screen)
                        <img src="{{ asset('storage/'.$screen->image_path) }}" class="h-32 rounded shadow">
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- TAB 3: Publish Type --}}
    <div x-show="tab === 'publish'" class="space-y-6">
        
        {{-- Publish Type Selector --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <label class="cursor-pointer border dark:border-gray-600 rounded-lg p-4 flex items-center hover:bg-gray-50 dark:hover:bg-dark-hover" :class="{ 'ring-2 ring-blue-500 bg-blue-50 dark:bg-blue-900/20': publishType === 'pwa_adhoc' }">
                <input type="radio" x-model="publishType" value="pwa_adhoc" class="sr-only">
                <div>
                    <strong class="block dark:text-white">PWA / Adhoc</strong>
                    <span class="text-xs text-gray-500">نسخه وب و ادهاک (قابل انتخاب همزمان)</span>
                </div>
            </label>

            <label class="cursor-pointer border dark:border-gray-600 rounded-lg p-4 flex items-center hover:bg-gray-50 dark:hover:bg-dark-hover" :class="{ 'ring-2 ring-blue-500 bg-blue-50 dark:bg-blue-900/20': publishType === 'internal' }">
                <input type="radio" x-model="publishType" value="internal" class="sr-only">
                <div>
                    <strong class="block dark:text-white">Internal (داخلی)</strong>
                    <span class="text-xs text-gray-500">هاست سیبانه</span>
                </div>
            </label>

            <label class="cursor-pointer border dark:border-gray-600 rounded-lg p-4 flex items-center hover:bg-gray-50 dark:hover:bg-dark-hover" :class="{ 'ring-2 ring-blue-500 bg-blue-50 dark:bg-blue-900/20': publishType === 'appstore' }">
                <input type="radio" x-model="publishType" value="appstore" class="sr-only">
                <div>
                    <strong class="block dark:text-white">AppStore (رسمی)</strong>
                    <span class="text-xs text-gray-500">لینک مستقیم اپ‌استور</span>
                </div>
            </label>
        </div>

        {{-- 1. PWA & Adhoc Fields --}}
        <div x-show="publishType === 'pwa_adhoc'" class="space-y-6">
            <div class="bg-gray-50 dark:bg-dark-bg p-4 rounded-lg border dark:border-gray-700">
                <label class="flex items-center mb-4">
                    <input type="checkbox" name="type_pwa" value="1" @checked(old('type_pwa', $product->type_pwa ?? false)) class="w-5 h-5 text-blue-600 rounded">
                    <span class="mr-2 font-bold dark:text-gray-300">فعال‌سازی نسخه PWA</span>
                </label>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pl-6">
                    <div>
                        <label class="block text-xs font-medium mb-1">قیمت PWA</label>
                        <input type="number" name="pwa_price" value="{{ old('pwa_price', $product->pwa_price ?? '') }}" class="w-full px-3 py-2 border rounded">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">آدرس فایل (URL)</label>
                        <input type="text" name="pwa_url" value="{{ old('pwa_url', $product->pwa_url ?? '') }}" class="w-full px-3 py-2 border rounded ltr">
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-dark-bg p-4 rounded-lg border dark:border-gray-700 opacity-50 cursor-not-allowed">
                <label class="flex items-center">
                    <input type="checkbox" disabled class="w-5 h-5 text-gray-400 rounded">
                    <span class="mr-2 font-bold text-gray-500">نسخه Adhoc (به زودی)</span>
                </label>
            </div>
        </div>

        {{-- 2. Internal Fields --}}
        <div x-show="publishType === 'internal'" class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg border border-purple-200 dark:border-purple-800">
            <input type="hidden" name="type_internal" :value="publishType === 'internal' ? 1 : 0">
            <h4 class="font-bold text-purple-800 dark:text-purple-300 mb-4">تنظیمات Internal</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium mb-1 dark:text-gray-300">قیمت</label>
                    <input type="number" name="internal_price" value="{{ old('internal_price', $product->internal_price ?? '') }}" class="w-full px-3 py-2 border rounded dark:bg-dark-paper dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 dark:text-gray-300">آدرس فایل (URL)</label>
                    <input type="text" name="internal_url" value="{{ old('internal_url', $product->internal_url ?? '') }}" class="w-full px-3 py-2 border rounded dark:bg-dark-paper dark:border-gray-600 dark:text-white ltr">
                </div>
            </div>
        </div>

        {{-- 3. AppStore Fields --}}
        <div x-show="publishType === 'appstore'" class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
            <input type="hidden" name="type_appstore" :value="publishType === 'appstore' ? 1 : 0">
            <h4 class="font-bold text-blue-800 dark:text-blue-300 mb-4">تنظیمات AppStore</h4>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium mb-1 dark:text-gray-300">لینک اپ‌استور (URL)</label>
                    <input type="text" name="native_appstore_url" value="{{ old('native_appstore_url', $product->native_appstore_url ?? '') }}" class="w-full px-3 py-2 border rounded dark:bg-dark-paper dark:border-gray-600 dark:text-white ltr">
                    <input type="hidden" name="appstore_link"> 
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium mb-1 dark:text-gray-300">نام کاربری (Apple ID)</label>
                        <input type="text" name="native_appstore_username" value="{{ old('native_appstore_username', $product->native_appstore_username ?? '') }}" class="w-full px-3 py-2 border rounded dark:bg-dark-paper dark:border-gray-600 dark:text-white ltr">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1 dark:text-gray-300">رمز عبور</label>
                        <input type="text" name="native_appstore_password" value="{{ old('native_appstore_password', $product->native_appstore_password ?? '') }}" class="w-full px-3 py-2 border rounded dark:bg-dark-paper dark:border-gray-600 dark:text-white ltr">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Submit --}}
    <div class="flex justify-end pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-8 rounded-lg shadow-md">
            ذخیره اپلیکیشن
        </button>
    </div>
</div>

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush