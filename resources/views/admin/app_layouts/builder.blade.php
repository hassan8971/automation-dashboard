@extends('admin.layouts.app')

@section('title', 'ویژوال بیلدر - ' . $page->title)

@section('styles')
<style>
    /* شبیه‌ساز موبایل */
    .mobile-frame {
        width: 375px;
        height: 750px;
        background: #fff;
        border: 12px solid #1a1a1a;
        border-radius: 40px;
        margin: 0 auto;
        position: relative;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        display: flex;
        flex-direction: column;
    }
    
    .mobile-content {
        flex: 1;
        overflow-y: auto;
        background-color: #f3f4f6;
        padding-bottom: 80px; 
    }

    /* استایل‌های انتخاب */
    .editable-element {
        position: relative;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.2s;
    }
    .editable-element:hover {
        border-color: #3b82f6;
    }
    .editable-element.is-selected {
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
        z-index: 10;
    }

    /* تب‌بار پایین */
    .mobile-tab-bar {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 65px;
        background: white;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-around;
        align-items: center;
        z-index: 50;
        padding-bottom: 5px; /* برای گوشی‌های جدید */
    }
    
    .tab-item {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        cursor: pointer;
        color: #9ca3af; /* خاکستری پیش‌فرض */
        transition: all 0.2s;
    }
    
    .tab-item.is-active {
        color: #2563eb; /* آبی فعال */
    }
    
    .tab-item:hover {
        background-color: #f9fafb;
    }

    .mobile-content::-webkit-scrollbar { width: 4px; }
    .mobile-content::-webkit-scrollbar-thumb { background-color: #ccc; border-radius: 4px; }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<div x-data="pageBuilder()" class="h-[calc(100vh-100px)] flex flex-col dir-ltr text-right">
    
    <div class="bg-white dark:bg-dark-paper border-b dark:border-gray-700 px-6 py-3 flex justify-between items-center shadow-sm z-20">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.layouts.index') }}" class="text-gray-500 hover:text-gray-800 dark:text-gray-300">
                <i class="fas fa-arrow-right"></i>
            </a>
            <h1 class="font-bold text-lg text-gray-800 dark:text-white">طراحی صفحه: <span class="text-blue-600">{{ $page->title }}</span></h1>
            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Live Editor</span>
        </div>
        <button @click="saveChanges()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-bold shadow transition flex items-center gap-2">
            <i class="fas fa-save"></i> ذخیره تغییرات
        </button>
    </div>

    <div class="flex-1 flex overflow-hidden bg-gray-100 dark:bg-dark-bg">
        
        <div class="w-80 bg-white dark:bg-dark-paper border-r dark:border-gray-700 overflow-y-auto p-4 transition-all" 
             x-show="selectedItem" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 -translate-x-full">
            
            <div class="flex justify-between items-center mb-4 border-b pb-2 dark:border-gray-700">
                <h3 class="font-bold text-gray-700 dark:text-gray-200">تنظیمات</h3>
                <button @click="selectedItem = null" class="text-gray-400 hover:text-red-500"><i class="fas fa-times"></i></button>
            </div>

            <template x-if="selectedItem">
                <div class="space-y-4">
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">
                            <span x-text="selectedItem.is_tab ? 'عنوان تب' : 'عنوان بخش'"></span>
                        </label>
                        <input type="text" x-model="selectedItem.title" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-dark-bg dark:text-white rounded-md">
                    </div>

                    <template x-if="selectedItem.type === 'slider_main'">
                        <div class="space-y-3">
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400">مدیریت اسلایدها</label>
                            
                            <div class="space-y-2">
                                <template x-for="(slide, index) in (selectedItem.config.slides || [])" :key="index">
                                    <div class="p-2 border rounded-lg bg-gray-50 dark:bg-dark-hover dark:border-gray-700 relative group">
                                        <div class="h-20 bg-gray-200 rounded mb-2 overflow-hidden relative">
                                            <img x-show="slide.image" :src="slide.image" class="w-full h-full object-cover">
                                            <div class="absolute inset-0 flex items-center justify-center bg-black/30 opacity-0 group-hover:opacity-100 transition cursor-pointer" 
                                                 @click="document.getElementById('slide_upload_' + index).click()">
                                                <i class="fas fa-camera text-white"></i>
                                            </div>
                                        </div>
                                        <input type="file" :id="'slide_upload_' + index" class="hidden" @change="uploadSlideImage($event, index)">
                                        
                                        <input type="text" x-model="slide.link" placeholder="لینک مقصد..." class="w-full text-xs border-gray-300 dark:bg-dark-bg rounded dark:border-gray-600 mb-1">
                                        
                                        <button @click="removeSlide(index)" class="absolute top-1 left-1 text-red-500 bg-white rounded-full w-5 h-5 flex items-center justify-center shadow hover:bg-red-50">
                                            <i class="fas fa-times text-[10px]"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <button @click="addSlide()" class="w-full py-2 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg text-gray-500 text-xs hover:bg-gray-50 dark:hover:bg-dark-hover">
                                + افزودن اسلاید جدید
                            </button>
                        </div>
                    </template>

                    <template x-if="selectedItem.type === 'list_horizontal'">
                        <div class="space-y-3 p-3 bg-gray-50 dark:bg-dark-hover rounded-lg border dark:border-gray-700">
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400">منبع داده</label>
                            <select x-model="selectedItem.source_type" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-dark-bg dark:text-white rounded-md">
                                <option value="auto">🤖 خودکار (هوشمند)</option>
                                <option value="manual">✋ دستی (انتخابی)</option>
                            </select>

                            <div x-show="selectedItem.source_type === 'auto'" class="space-y-2">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">سناریو نمایش</label>
                                    <select x-model="selectedItem.config.sort_type" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-dark-bg dark:text-white rounded-md">
                                        <option value="newest">🔥 جدیدترین‌ها</option>
                                        <option value="popular">⭐ محبوب‌ترین (امتیاز بالا)</option>
                                        <option value="most_downloaded">📥 پر دانلودترین</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">تعداد</label>
                                    <input type="number" x-model="selectedItem.config.limit" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-dark-bg dark:text-white rounded-md">
                                </div>
                            </div>

                            <div x-show="selectedItem.source_type === 'manual'" class="mt-2">
                                <button @click="openAppSelector()" type="button" class="w-full bg-white dark:bg-dark-bg border border-blue-200 dark:border-blue-900 hover:bg-blue-50 text-xs py-2 rounded-lg text-blue-600 font-bold flex items-center justify-center gap-2">
                                    <i class="fas fa-plus-circle"></i> انتخاب دستی
                                </button>
                                <div class="mt-2 text-xs text-gray-500 text-center">
                                    <span x-text="(selectedItem.config.manual_ids || []).length"></span> مورد انتخاب شده
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="selectedItem.type === 'banner_single'">
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">تصویر بنر</label>
                                <div class="relative w-full h-32 bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center cursor-pointer hover:bg-gray-50 overflow-hidden"
                                     @click="$refs.bannerInput.click()">
                                    <img x-show="selectedItem.config.image" :src="selectedItem.config.image" class="w-full h-full object-cover absolute inset-0">
                                    <div x-show="!selectedItem.config.image" class="text-gray-400 flex flex-col items-center">
                                        <i class="fas fa-cloud-upload-alt fa-2x"></i>
                                        <span class="text-xs mt-1">آپلود</span>
                                    </div>
                                </div>
                                <input type="file" x-ref="bannerInput" class="hidden" @change="uploadBannerImage($event)">
                            </div>
                            
                            <div>
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">لینک مقصد</label>
                                <input type="text" x-model="selectedItem.config.link" placeholder="https://..." class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-dark-bg dark:text-white rounded-md dir-ltr text-left">
                            </div>
                        </div>
                    </template>

                    <template x-if="selectedItem.is_tab">
                        <div class="space-y-3 p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg border border-indigo-100 dark:border-indigo-800">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">کلاس آیکون (FontAwesome)</label>
                                <div class="flex gap-2">
                                    <input type="text" x-model="selectedItem.icon" placeholder="fas fa-home" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-dark-bg dark:text-white rounded-md font-mono dir-ltr text-left">
                                    <div class="w-9 h-9 bg-white dark:bg-dark-hover border rounded flex items-center justify-center text-blue-600">
                                        <i :class="selectedItem.icon"></i>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">لینک مقصد (Slug)</label>
                                <input type="text" x-model="selectedItem.link" placeholder="home" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-dark-bg dark:text-white rounded-md dir-ltr text-left">
                                <p class="text-[10px] text-gray-400 mt-1">برای اتصال به این صفحه، مقدار را <b>{{ $page->slug }}</b> بگذارید.</p>
                            </div>
                        </div>
                    </template>
                    
                    <template x-if="!selectedItem.is_tab">
                        <div class="pt-4 border-t dark:border-gray-700">
                            <button @click="deleteItem(selectedItem)" class="w-full text-red-600 border border-red-200 hover:bg-red-50 dark:hover:bg-red-900/20 py-2 rounded-md text-sm transition">
                                <i class="fas fa-trash"></i> حذف این بخش
                            </button>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <div class="flex-1 flex justify-center items-center p-8 overflow-auto relative bg-dot-pattern">
            <div class="mobile-frame">
                <div class="h-6 bg-black w-full flex justify-between px-4 items-center text-white text-[10px]">
                    <span>9:41</span>
                    <div class="flex gap-1"><i class="fas fa-signal"></i><i class="fas fa-wifi"></i><i class="fas fa-battery-full"></i></div>
                </div>

                <div id="canvas-area" class="mobile-content relative">
                    <div class="px-4 py-3 bg-white flex justify-between items-center sticky top-0 z-40 shadow-sm">
                        <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
                        <span class="font-bold text-gray-800">سیبانه</span>
                        <i class="fas fa-search text-gray-500"></i>
                    </div>

                    <div id="sortable-sections" class="pb-4 min-h-[200px]">
                        <template x-for="section in sections" :key="section.id">
                            <div class="editable-element relative group" 
                                 :class="{'is-selected': selectedItem && selectedItem.id === section.id}"
                                 @click.stop="selectItem(section)">
                                
                                <template x-if="section.type === 'slider_main'">
                                    <div class="mt-4 px-4">
                                        <div class="h-40 bg-gray-200 rounded-xl flex items-center justify-center relative overflow-hidden">
                                            <template x-if="section.config.slides && section.config.slides.length > 0 && section.config.slides[0].image">
                                                <img :src="section.config.slides[0].image" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!section.config.slides || section.config.slides.length === 0 || !section.config.slides[0].image">
                                                <div class="text-gray-400 flex flex-col items-center">
                                                    <i class="fas fa-images fa-2x mb-1"></i>
                                                    <span class="text-xs">اسلایدر</span>
                                                </div>
                                            </template>
                                            <div class="absolute bottom-2 flex gap-1 justify-center w-full">
                                                <template x-for="s in (section.config.slides || [])">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-white shadow"></div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="section.type === 'list_horizontal'">
                                    <div class="mt-6">
                                        <div class="flex justify-between px-4 mb-2">
                                            <h3 class="font-bold text-gray-800 text-sm" x-text="section.title"></h3>
                                            <span class="text-blue-500 text-xs">مشاهده همه</span>
                                        </div>
                                        <div class="flex gap-3 overflow-x-auto px-4 pb-2 no-scrollbar">
                                            <template x-for="app in getPreviewApps(section)" :key="app.id">
                                                <div class="min-w-[90px] w-[90px] flex flex-col gap-1 cursor-default">
                                                    <img :src="app.icon" class="w-[90px] h-[90px] rounded-2xl shadow-sm border border-gray-100 object-cover bg-white">
                                                    <span class="text-xs font-medium text-gray-700 truncate" x-text="app.title"></span>
                                                    <span class="text-[10px] text-gray-400" x-text="app.price"></span>
                                                </div>
                                            </template>
                                            <div x-show="getPreviewApps(section).length === 0" class="w-full text-center text-xs text-gray-400 py-4">
                                                ...
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                
                                <template x-if="section.type === 'banner_single'">
                                    <div class="mt-4 px-4">
                                        <div class="h-32 bg-gray-100 rounded-xl border border-gray-200 flex items-center justify-center overflow-hidden">
                                            <template x-if="section.config.image">
                                                <img :src="section.config.image" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!section.config.image">
                                                <span class="text-gray-400 font-bold">بنر</span>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <button @click.stop="deleteItem(section)" class="absolute -top-2 -right-2 bg-red-500 text-white w-5 h-5 rounded-full text-xs hidden group-hover:flex items-center justify-center z-50 shadow">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="mobile-tab-bar">
    <template x-for="item in appTabs" :key="item.id">
        <div class="tab-item" 
             :class="{
                 'is-active': isTabActive(item),
                 'border-t-2 border-blue-500 bg-blue-50': selectedItem && selectedItem.id === item.id
             }"
             @click="selectItem(item, true)">
            
            <template x-if="item.image">
                <img :src="item.image" class="w-6 h-6 object-contain mb-1">
            </template>

            <template x-if="!item.image">
                <i :class="item.icon || 'fas fa-circle'" class="text-xl mb-1"></i>
            </template>

            <span class="text-[10px]" x-text="item.title || item.name"></span>
        
        </div>
    </template>
</div>
            </div>
        </div>

        <div class="w-64 bg-white dark:bg-dark-paper border-l dark:border-gray-700 p-4">
            <h3 class="font-bold text-gray-700 dark:text-gray-200 mb-4 text-right">ابزارها</h3>
            <div class="space-y-2">
                <button @click="addSection('slider_main')" class="w-full flex items-center gap-3 p-3 bg-gray-50 dark:bg-dark-hover hover:bg-white hover:shadow-md border dark:border-gray-600 rounded-lg transition text-right">
                    <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded flex items-center justify-center"><i class="fas fa-images"></i></div>
                    <div class="text-sm font-medium dark:text-gray-200">اسلایدر بزرگ</div>
                </button>
                <button @click="addSection('list_horizontal')" class="w-full flex items-center gap-3 p-3 bg-gray-50 dark:bg-dark-hover hover:bg-white hover:shadow-md border dark:border-gray-600 rounded-lg transition text-right">
                    <div class="w-8 h-8 bg-green-100 text-green-600 rounded flex items-center justify-center"><i class="fas fa-list"></i></div>
                    <div class="text-sm font-medium dark:text-gray-200">لیست افقی</div>
                </button>
                <button @click="addSection('banner_single')" class="w-full flex items-center gap-3 p-3 bg-gray-50 dark:bg-dark-hover hover:bg-white hover:shadow-md border dark:border-gray-600 rounded-lg transition text-right">
                    <div class="w-8 h-8 bg-purple-100 text-purple-600 rounded flex items-center justify-center"><i class="fas fa-ad"></i></div>
                    <div class="text-sm font-medium dark:text-gray-200">بنر تکی</div>
                </button>
            </div>
        </div>
    </div>

    <div x-show="showAppSelector" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak>
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showAppSelector = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <template x-if="selectedItem">
                <div class="relative w-full max-w-2xl bg-white dark:bg-dark-paper rounded-2xl shadow-2xl border dark:border-gray-700">
                    <div class="p-4 border-b dark:border-gray-700 flex justify-between">
                        <h3 class="font-bold text-gray-800 dark:text-white">انتخاب اپلیکیشن‌ها</h3>
                        <button @click="showAppSelector = false"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="p-4 border-b dark:border-gray-700">
                        <input x-model="manualSearch" type="text" placeholder="جستجو..." class="w-full rounded-xl border-gray-300 dark:bg-dark-bg dark:text-white">
                    </div>
                    <div class="max-h-[400px] overflow-y-auto p-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                        <template x-for="product in allProducts.filter(p => p.title.toLowerCase().includes(manualSearch.toLowerCase()))" :key="product.id">
                            <label class="flex items-center gap-3 p-3 rounded-xl border dark:border-gray-700 cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900/20" :class="{'border-blue-500 bg-blue-50 dark:bg-blue-900/20': selectedItem.config.manual_ids && selectedItem.config.manual_ids.includes(product.id)}">
                                <input type="checkbox" :value="product.id" x-model="selectedItem.config.manual_ids" class="rounded text-blue-600">
                                <img :src="product.icon" class="w-10 h-10 rounded-lg object-cover">
                                <div class="flex-1 text-right">
                                    <div class="text-sm font-bold dark:text-white" x-text="product.title"></div>
                                    <div class="text-xs text-gray-500" x-text="product.price"></div>
                                </div>
                            </label>
                        </template>
                    </div>
                    <div class="p-4 flex justify-end">
                        <button @click="showAppSelector = false" class="bg-blue-600 text-white px-6 py-2 rounded-lg">تایید</button>
                    </div>
                </div>
            </template>
        </div>
    </div>

</div>

<script>
    function pageBuilder() {
        return {
            sections: @json($page->sections),
            appTabs: {!! $appTabs->map(function($tab) {
    return [
        'id' => $tab->id,
        'title' => $tab->title,
        'link' => $tab->link,
        'icon' => $tab->icon,
        // اگر عکس دارد، آدرس کاملش را بساز، وگرنه نال بگذار
        'image' => $tab->image_path ? asset('storage/'.$tab->image_path) : null,
    ];
})->toJson() !!},
            pageSlug: '{{ $page->slug }}', // اسلاگ صفحه فعلی برای تشخیص تب فعال
            selectedItem: null,
            showAppSelector: false,
            manualSearch: '',
            
            allProducts: {!! $products->map(fn($p) => [
                'id' => $p->id,
                'title' => $p->title,
                'price' => $p->price > 0 ? number_format($p->price) : 'رایگان',
                'icon' => $p->icon_path ? asset('storage/'.$p->icon_path) : asset('images/default-icon.png'),
                'download_count' => $p->download_count ?? 0,
                'rating' => $p->rating ?? 0,
            ])->toJson() !!},

            init() {
                Sortable.create(document.getElementById('sortable-sections'), {
                    animation: 150,
                    ghostClass: 'opacity-50',
                    onEnd: (evt) => { /* Logic */ }
                });
            },

            // --- تابع جدید: تشخیص تب فعال ---
            isTabActive(item) {
                // اگر لینک منو دقیقاً برابر با اسلاگ صفحه فعلی باشد، فعال است
                // مثلا home == home
                // نکته: اسلش‌های اول لینک را حذف می‌کنیم تا مقایسه دقیق‌تر باشد
                const link = (item.link_url || '').replace(/^\//, '');
                return link === this.pageSlug;
            },

            addSection(type) {
                const newId = 'new_' + Date.now();
                let config = {};
                if(type === 'slider_main') config = { slides: [] };
                if(type === 'list_horizontal') config = { limit: 10, sort_type: 'newest', manual_ids: [] };
                if(type === 'banner_single') config = { image: null, link: '' };

                this.sections.push({
                    id: newId, type: type, title: 'بخش جدید',
                    source_type: 'auto', config: config, is_new: true
                });
                
                this.$nextTick(() => {
                    const c = document.getElementById('canvas-area'); c.scrollTop = c.scrollHeight;
                });
            },

            selectItem(item, isTab = false) {
                this.selectedItem = item;
                this.selectedItem.is_tab = isTab; // <--- تغییر نام فلگ
                
                if(!isTab) {
                    if(!this.selectedItem.config) this.selectedItem.config = {};
                    if(item.type === 'slider_main' && !this.selectedItem.config.slides) this.selectedItem.config.slides = [];
                    if(item.type === 'list_horizontal' && !this.selectedItem.config.manual_ids) this.selectedItem.config.manual_ids = [];
                }
            },

            deleteItem(item) {
                if(!confirm('حذف شود؟')) return;
                this.sections = this.sections.filter(s => s.id !== item.id);
                this.selectedItem = null;
            },

            getPreviewApps(section) {
                if (!section.config) return [];
                let apps = [];
                if (section.source_type === 'manual') {
                    const rawIds = section.config.manual_ids || [];
                    const ids = rawIds.map(id => parseInt(id)); 
                    apps = this.allProducts.filter(p => ids.includes(p.id));
                } else {
                    let sorted = [...this.allProducts];
                    const sortType = section.config.sort_type || 'newest';
                    if (sortType === 'newest') sorted.sort((a, b) => b.id - a.id);
                    else if (sortType === 'popular') sorted.sort((a, b) => b.rating - a.rating);
                    else if (sortType === 'most_downloaded') sorted.sort((a, b) => b.download_count - a.download_count);
                    apps = sorted.slice(0, section.config.limit || 10);
                }
                return apps;
            },

            async uploadImage(file) {
                const formData = new FormData();
                formData.append('image', file);
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                try {
                    const res = await fetch('{{ route("admin.layouts.upload_image") }}', {
                        method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': token }
                    });
                    const data = await res.json();
                    return data.success ? data.url : null;
                } catch(e) { return null; }
            },

            async uploadSlideImage(event, index) {
                const file = event.target.files[0];
                if(!file) return;
                const url = await this.uploadImage(file);
                if(url) this.selectedItem.config.slides[index].image = url;
            },

            async uploadBannerImage(event) {
                const file = event.target.files[0];
                if(!file) return;
                const url = await this.uploadImage(file);
                if(url) this.selectedItem.config.image = url;
            },
            
            openAppSelector() { this.showAppSelector = true; },

            // باقی متدها (addSlide, removeSlide, saveChanges) دقیقاً مثل قبل...
            addSlide() {
                if(!this.selectedItem.config.slides) this.selectedItem.config.slides = [];
                this.selectedItem.config.slides.push({ image: null, link: '' });
            },
            removeSlide(index) {
                this.selectedItem.config.slides.splice(index, 1);
            },
            saveChanges() {
                const payload = { sections: this.sections, appTabs: this.appTabs };
                fetch('{{ route("admin.layouts.save_all", $page->id) }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(data => { alert('ذخیره شد!'); window.location.reload(); })
                .catch(err => alert('خطا در ذخیره'));
            }
        }
    }
</script>
@endsection