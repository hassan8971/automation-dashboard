@extends('admin.layouts.app')

@section('title', 'ویژوال بیلدر - ' . $page->title)

@push('styles')
<style>
    /* --- iPhone 17 Pro Style Frame --- */
    .mobile-wrapper {
        width: 390px;
        height: 100%;
        background: #000000;
        border-radius: 55px;
        padding: 10px;
        margin: 0 auto;
        position: relative;
        box-shadow: inset 0 0 2px 1px rgba(255,255,255,0.1), /* بازتاب نور لبه داخلی */
                0 20px 40px -10px rgba(0, 0, 0, 0.4);
    }

    .mobile-screen {
        /* این قسمت صفحه نمایش روشن داخل گوشی است */
        width: 100%;
        height: 100%;
        background-color: #f3f4f6; /* رنگ پس‌زمینه محتوا */
        border-radius: 46px; /* شعاع گردی هماهنگ با بدنه بیرونی */
        overflow: hidden;
        position: relative;
        display: flex;
        flex-direction: column;
        /* یک سایه داخلی ظریف برای جدا کردن صفحه از حاشیه */
        box-shadow: inset 0 0 0 1px rgba(0,0,0,0.1); 
    }
    
    /* داینامیک آیلند (کپسول بالای صفحه) */
    .dynamic-island {
        position: absolute;
        top: 11px;
        left: 50%;
        transform: translateX(-50%);
        width: 120px;
        height: 35px;
        background: #000;
        border-radius: 20px;
        z-index: 60; /* باید روی همه چیز باشد */
        display: flex;
        align-items: center;
        justify-content: center;
    }
    /* لنز دوربین داخل داینامیک آیلند */
    .dynamic-island::after {
        content: '';
        width: 10px;
        height: 10px;
        background: #1a1a1a;
        border-radius: 50%;
        position: absolute;
        right: 25%;
    }

    /* استاتوس بار جدید (ساعت و آیکون‌ها در گوشه‌ها) */
    .ios-status-bar {
        height: 44px;
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 20px;
        font-size: 12px;
        font-weight: 600;
        color: #000;
        position: absolute;
        top: 5px;
        z-index: 55; /* زیر داینامیک آیلند */
    }

    /* محتوای اصلی */
    .mobile-content {
        flex: 1;
        overflow-y: auto;
        padding-top: 50px; /* فاصله از زیر هدر */
        padding-bottom: 80px; /* فاصله از بالای تب‌بار */
        /* مخفی کردن اسکرول بار */
        scrollbar-width: none; 
        -ms-overflow-style: none;
    }
    .mobile-content::-webkit-scrollbar { display: none; }


    /* --- بقیه استایل‌ها (بدون تغییر) --- */
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
    
    /* تب‌بار پایین (با کمی اصلاح گوشه‌ها) */
    .mobile-tab-bar {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 75px; /* کمی بلندتر برای فضای Home Indicator */
        background: white;
        border-top: 1px solid #e5e7eb;
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        align-items: center;
        z-index: 50;
        padding-bottom: 15px; /* فضای خالی پایین برای آیفون‌های جدید */
        border-bottom-left-radius: 46px; /* هماهنگی با گردی صفحه */
        border-bottom-right-radius: 46px;
    }
    /* خط Home Indicator پایین صفحه */
    .mobile-tab-bar::after {
        content: '';
        position: absolute;
        bottom: 5px;
        left: 50%;
        transform: translateX(-50%);
        width: 130px;
        height: 5px;
        background: #000;
        border-radius: 10px;
        opacity: 0.8;
    }

    .tab-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        cursor: pointer;
        color: #9ca3af;
        transition: all 0.2s;
        position: relative;
    }
    .tab-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        pointer-events: none;
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
@endpush

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<div x-data="pageBuilder()" class="h-[calc(100vh-35px)] flex flex-col dir-ltr text-right">
    
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
                <button @click="selectedItem = null" class="text-gray-400 hover:text-red-500">
                    <x-icons.close />
                </button>
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
                                            <x-icons.close />
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
                        <div class="space-y-4 p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg border border-indigo-100 dark:border-indigo-800"
                            x-data="{ tabMode: selectedItem.image ? 'image' : 'font' }">
                            
                            <div>
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-2">نوع نمایش</label>
                                <div class="flex bg-white dark:bg-dark-bg rounded-lg p-1 border dark:border-gray-600">
                                    <button @click="tabMode = 'font'; selectedItem.image = null; selectedItem.image_path = null" 
                                            :class="{'bg-indigo-100 text-indigo-700': tabMode === 'font', 'text-gray-500': tabMode !== 'font'}"
                                            class="flex-1 py-1.5 text-xs font-bold rounded transition">
                                        <i class="fas fa-font ml-1"></i> فونت
                                    </button>
                                    <button @click="tabMode = 'image'" 
                                            :class="{'bg-indigo-100 text-indigo-700': tabMode === 'image', 'text-gray-500': tabMode !== 'image'}"
                                            class="flex-1 py-1.5 text-xs font-bold rounded transition">
                                        <i class="fas fa-image ml-1"></i> تصویر
                                    </button>
                                </div>
                            </div>

                            <div x-show="tabMode === 'font'">
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">کلاس آیکون</label>
                                <div class="flex gap-2">
                                    <input type="text" x-model="selectedItem.icon" placeholder="fas fa-home" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-dark-bg dark:text-white rounded-md font-mono dir-ltr text-left">
                                    <div class="w-9 h-9 bg-white dark:bg-dark-hover border rounded flex items-center justify-center text-blue-600">
                                        <i :class="selectedItem.icon"></i>
                                    </div>
                                </div>
                            </div>

                            <div x-show="tabMode === 'image'">
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">آپلود آیکون</label>
                                
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-white border rounded-lg flex items-center justify-center overflow-hidden relative cursor-pointer hover:border-blue-500 transition"
                                        @click="$refs.tabImageInput.click()">
                                        
                                        <template x-if="selectedItem.image">
                                            <img :src="selectedItem.image" class="w-8 h-8 object-contain">
                                        </template>
                                        
                                        <template x-if="!selectedItem.image">
                                            <i class="fas fa-plus text-gray-300"></i>
                                        </template>
                                        
                                        <div class="absolute inset-0 bg-black/40 opacity-0 hover:opacity-100 flex items-center justify-center text-white text-xs">
                                            <i class="fas fa-pen"></i>
                                        </div>
                                    </div>

                                    <div class="flex-1">
                                        <button @click="$refs.tabImageInput.click()" class="text-xs text-blue-600 font-bold hover:underline">
                                            انتخاب تصویر جدید
                                        </button>
                                        <p class="text-[10px] text-gray-400 mt-1">سایز پیشنهادی: 64x64 پیکسل</p>
                                    </div>

                                    <input type="file" x-ref="tabImageInput" class="hidden" @change="uploadTabIcon($event)">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">لینک مقصد (Slug)</label>
                                <input type="text" x-model="selectedItem.link" placeholder="home" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-dark-bg dark:text-white rounded-md dir-ltr text-left">
                            </div>
                            
                            <div class="pt-2 border-t dark:border-indigo-800 mt-2">
                                <button @click="deleteTab(selectedItem)" class="w-full text-red-500 text-xs py-2 hover:bg-red-50 rounded transition flex items-center justify-center gap-1">
                                    <i class="fas fa-trash"></i> حذف این تب
                                </button>
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
    
            <div class="mobile-wrapper">
                
                <div class="mobile-screen">
                    
                    <div class="ios-status-bar">
                        <span>9:41</span>
                        <div class="flex gap-1.5 text-[10px]">
                            <i class="fas fa-signal"></i>
                            <i class="fas fa-wifi"></i>
                            <i class="fas fa-battery-full text-[12px]"></i>
                        </div>
                    </div>

                    <div class="dynamic-island"></div>

                    <div id="canvas-area" class="mobile-content relative">
                        
                        <div id="sortable-sections" class="pb-4 min-h-[200px]">
                            <template x-for="section in sections" :key="section.id">
                                <div class="editable-element relative group" 
                                        :class="{'is-selected': selectedItem && selectedItem.id === section.id}"
                                        @click.stop="selectItem(section)">
                                    
                                    <template x-if="section.type === 'slider_main'">
                                        <div class="mt-4 px-4" x-data="{ currentSlide: 0 }">
                                            <div class="h-40 bg-gray-200 rounded-xl flex items-center justify-center relative overflow-hidden group">
                                                <template x-if="section.config.slides && section.config.slides.length > 0">
                                                    <img :src="section.config.slides[currentSlide]?.image || 'https://via.placeholder.com/400x200?text=No+Image'" 
                                                            class="w-full h-full object-cover transition-opacity duration-300">
                                                </template>
                                                <template x-if="!section.config.slides || section.config.slides.length === 0">
                                                    <div class="text-gray-400 flex flex-col items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 mb-1"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 6v12a2.25 2.25 0 002.25 2.25zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                                                        <span class="text-xs">اسلایدر خالی</span>
                                                    </div>
                                                </template>
                                                <template x-if="section.config.slides && section.config.slides.length > 1">
                                                    <div class="absolute inset-0 flex justify-between items-center px-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                        <button @click.stop="currentSlide = currentSlide === 0 ? section.config.slides.length - 1 : currentSlide - 1" class="w-6 h-6 bg-black/30 hover:bg-black/50 text-white rounded-full flex items-center justify-center backdrop-blur-sm transition">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                                                        </button>
                                                        <button @click.stop="currentSlide = currentSlide === section.config.slides.length - 1 ? 0 : currentSlide + 1" class="w-6 h-6 bg-black/30 hover:bg-black/50 text-white rounded-full flex items-center justify-center backdrop-blur-sm transition">
                                                        
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg></button>
                                                    </div>
                                                </template>
                                                <div class="absolute bottom-2 flex gap-1 justify-center w-full z-10">
                                                    <template x-for="(s, idx) in (section.config.slides || [])" :key="idx">
                                                        <div class="w-1.5 h-1.5 rounded-full shadow transition-all duration-300" :class="currentSlide === idx ? 'bg-white w-3' : 'bg-white/50'"></div>
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
                                        <x-icons.close />
                                    </button>
                                </div>
                                </template>
                        </div>
                    </div>

                    <div class="mobile-tab-bar relative">
                        <template x-for="item in appTabs" :key="'tab_' + item.id">
                            <div class="tab-item hover:bg-gray-50" 
                                    :class="{
                                        'is-active': isTabActive(item),
                                        'text-blue-600': selectedItem && selectedItem.id === item.id,
                                        'text-gray-400': !selectedItem || selectedItem.id !== item.id
                                    }"
                                    @click="selectItem(item, true)">
                                <template x-if="item.image"><img :src="item.image" class="w-6 h-6 object-contain mb-1"></template>
                                <template x-if="!item.image"><i :class="item.icon || 'fas fa-circle'" class="text-xl mb-1"></i></template>
                                <span class="text-[9px] font-medium" x-text="item.title || item.name"></span>
                            </div>
                        </template>
                        <template x-for="i in Math.max(0, 5 - appTabs.length)" :key="'new_' + i">
                            <div class="tab-item group" @click="openTabCreator(appTabs.length + i + 1)">
                                <div class="w-10 h-10 rounded-full border-2 border-dashed border-gray-300 flex items-center justify-center text-gray-300 group-hover:border-blue-500 group-hover:text-blue-500 group-hover:scale-110 transition-all cursor-pointer bg-gray-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                </div>
                                <span class="text-[9px] text-gray-300 mt-1 group-hover:text-blue-500">افزودن</span>
                            </div>
                        </template>
                        
                        <div x-show="showTabCreator" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full opacity-0" x-transition:enter-end="translate-y-0 opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="translate-y-full opacity-0" class="absolute bottom-0 left-0 right-0 bg-white shadow-[0_-5px_20px_rgba(0,0,0,0.15)] rounded-t-2xl z-[60] p-4 border-t border-gray-100" @click.outside="showTabCreator = false" style="border-bottom-left-radius: 46px; border-bottom-right-radius: 46px;">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-sm font-bold text-gray-800">ایجاد تب جدید</h4>
                                <button @click="showTabCreator = false" class="text-gray-400 hover:text-red-500 transition p-1 rounded-md hover:bg-gray-100"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg></button>
                            </div>
                            <div class="space-y-3">
                                <div class="grid grid-cols-2 gap-2"><div><label class="block text-[10px] font-bold text-gray-500 mb-1">عنوان</label><input type="text" x-model="newTabForm.title" placeholder="خانه" class="w-full text-xs border-gray-300 rounded focus:border-blue-500"></div><div><label class="block text-[10px] font-bold text-gray-500 mb-1">لینک (Slug)</label><input type="text" x-model="newTabForm.link" placeholder="home" class="w-full text-xs border-gray-300 rounded focus:border-blue-500 dir-ltr text-left"></div></div>
                                <div><label class="block text-[10px] font-bold text-gray-500 mb-1">آیکون</label><div class="flex items-center gap-2"><div class="flex bg-gray-100 rounded p-1"><button type="button" @click="newTabForm.iconType = 'font'" :class="{'bg-white shadow text-blue-600': newTabForm.iconType === 'font', 'text-gray-400': newTabForm.iconType !== 'font'}" class="px-2 py-1 rounded text-[10px] transition"><i class="fas fa-font"></i></button><button type="button" @click="newTabForm.iconType = 'image'" :class="{'bg-white shadow text-blue-600': newTabForm.iconType === 'image', 'text-gray-400': newTabForm.iconType !== 'image'}" class="px-2 py-1 rounded text-[10px] transition"><i class="fas fa-image"></i></button></div><div class="flex-1"><input x-show="newTabForm.iconType === 'font'" type="text" x-model="newTabForm.icon" class="w-full text-xs border-gray-300 rounded font-mono dir-ltr"><input x-show="newTabForm.iconType === 'image'" type="file" @change="handleNewTabImage" class="w-full text-[10px] text-gray-500"></div><div class="w-8 h-8 flex items-center justify-center bg-gray-50 border rounded text-blue-500"><i x-show="newTabForm.iconType === 'font'" :class="newTabForm.icon"></i><i x-show="newTabForm.iconType === 'image'" class="fas fa-check" :class="{'opacity-100': newTabForm.image, 'opacity-0': !newTabForm.image}"></i></div></div></div>
                                <button @click="createNewTab()" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-xs font-bold shadow-md transition transform active:scale-95">+ ایجاد و افزودن به لیست</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="w-64 bg-white dark:bg-dark-paper border-l dark:border-gray-700 p-4">
            <h3 class="font-bold text-gray-700 dark:text-gray-200 mb-4 text-right">ابزارها</h3>
            <p class="text-[10px] text-gray-400 mb-2">برای افزودن، بکشید و رها کنید</p>
            
            <div id="tools-list" class="space-y-2">
                
                <div data-type="slider_main" class="tool-item w-full flex items-center gap-3 p-3 bg-gray-50 dark:bg-dark-hover hover:bg-white hover:shadow-md border dark:border-gray-600 rounded-lg transition text-right cursor-grab active:cursor-grabbing">
                    <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded flex items-center justify-center pointer-events-none">
                        <x-icons.image />
                    </div>
                    <div class="text-sm font-medium dark:text-gray-200 pointer-events-none">اسلایدر بزرگ</div>
                </div>

                <div data-type="list_horizontal" class="tool-item w-full flex items-center gap-3 p-3 bg-gray-50 dark:bg-dark-hover hover:bg-white hover:shadow-md border dark:border-gray-600 rounded-lg transition text-right cursor-grab active:cursor-grabbing">
                    <div class="w-8 h-8 bg-green-100 text-green-600 rounded flex items-center justify-center pointer-events-none">
                        <x-icons.list />
                    </div>
                    <div class="text-sm font-medium dark:text-gray-200 pointer-events-none">لیست افقی</div>
                </div>

                <div data-type="banner_single" class="tool-item w-full flex items-center gap-3 p-3 bg-gray-50 dark:bg-dark-hover hover:bg-white hover:shadow-md border dark:border-gray-600 rounded-lg transition text-right cursor-grab active:cursor-grabbing">
                    <div class="w-8 h-8 bg-purple-100 text-purple-600 rounded flex items-center justify-center pointer-events-none">
                        <x-icons.ad />
                    </div>
                    <div class="text-sm font-medium dark:text-gray-200 pointer-events-none">بنر تکی</div>
                </div>

            </div>
        </div>
    </div>

    <div x-show="showAppSelector" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak>
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showAppSelector = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            
            <template x-if="selectedItem && selectedItem.config">
                
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
                            
                            <label class="flex items-center gap-3 p-3 rounded-xl border dark:border-gray-700 cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900/20" 
                                   :class="{
                                       'border-blue-500 bg-blue-50 dark:bg-blue-900/20': 
                                       selectedItem.config.manual_ids && selectedItem.config.manual_ids.includes(product.id)
                                   }">
                                
                                <input type="checkbox" :value="product.id" x-model="selectedItem.config.manual_ids" class="rounded text-blue-600">
                                <img :src="product.icon" class="w-10 h-10 rounded-lg object-cover">
                                <div class="flex-1 text-right">
                                    <div class="text-sm font-bold dark:text-white" x-text="product.title"></div>
                                    <div class="text-xs text-gray-500" x-text="product.price"></div>
                                </div>
                            </label>
                        </template>
                    </div>
                    
                    <div class="p-4 flex justify-end bg-gray-50 dark:bg-dark-hover rounded-b-2xl">
                        <button @click="showAppSelector = false" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold">
                            تایید (<span x-text="selectedItem.config.manual_ids ? selectedItem.config.manual_ids.length : 0"></span>)
                        </button>
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
                    'image_path' => $tab->image_path,
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
                // 1. تنظیم لیست وسط (Canvas)
                Sortable.create(document.getElementById('sortable-sections'), {
                    group: {
                        name: 'builder-group', // نام گروه مشترک
                        pull: true,
                        put: true // اجازه دریافت آیتم
                    },
                    animation: 150,
                    ghostClass: 'opacity-50',
                    draggable: '.editable-element', // فقط سکشن‌ها قابل جابجایی باشند
                    
                    // وقتی آیتمی از ابزارها به اینجا انداخته شد
                    onAdd: (evt) => {
                        // 1. نوع ابزار را از اتریبیوت HTML می‌خوانیم
                        const type = evt.item.getAttribute('data-type');
                        
                        // 2. المان HTML که Sortable ساخته را حذف می‌کنیم (چون Alpine خودش می‌سازد)
                        evt.item.remove();
                        
                        // 3. داده را به آرایه اضافه می‌کنیم (در ایندکس رها شده)
                        if (type) {
                            this.addSectionAt(type, evt.newIndex);
                        }
                    },

                    // وقتی آیتم‌های داخلی جابجا شدند (Reorder)
                    onEnd: (evt) => {
                        if (evt.from === evt.to) { // فقط اگر جابجایی داخلی بود
                            const rawSections = Alpine.raw(this.sections);
                            const item = rawSections[evt.oldIndex];
                            rawSections.splice(evt.oldIndex, 1);
                            rawSections.splice(evt.newIndex, 0, item);
                        }
                    }
                });

                // 2. تنظیم لیست ابزارها (Sidebar)
                Sortable.create(document.getElementById('tools-list'), {
                    group: {
                        name: 'builder-group',
                        pull: 'clone', // کپی کردن به جای جابجایی
                        put: false // اجازه نده چیزی برگردد به اینجا
                    },
                    sort: false, // اجازه نده ابزارها جابجا شوند
                    animation: 150
                });
            },

            // تابع جدید برای افزودن در موقعیت خاص (Drop)
            addSectionAt(type, index) {
                const newId = 'new_' + Date.now();
                let config = {};
                
                if(type === 'slider_main') config = { slides: [] };
                if(type === 'list_horizontal') config = { limit: 10, sort_type: 'newest', manual_ids: [] };
                if(type === 'banner_single') config = { image: null, link: '' };

                const newSection = {
                    id: newId, 
                    type: type, 
                    title: 'بخش جدید',
                    source_type: 'auto', 
                    config: config, 
                    is_new: true
                };

                // اضافه کردن به آرایه در ایندکس مشخص شده
                // اگر ایندکس تعریف نشده بود (کلیک معمولی)، به ته لیست اضافه کن
                if (index !== undefined && index !== null) {
                    this.sections.splice(index, 0, newSection);
                } else {
                    this.sections.push(newSection);
                    this.$nextTick(() => {
                        const c = document.getElementById('canvas-area'); c.scrollTop = c.scrollHeight;
                    });
                }
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
                this.selectedItem.is_tab = isTab;
                
                // FIX: همیشه مطمئن شویم config وجود دارد تا ارور undefined ندهد
                if (!this.selectedItem.config) {
                    this.selectedItem.config = {};
                }

                if(!isTab) {
                    if(item.type === 'slider_main' && !this.selectedItem.config.slides) this.selectedItem.config.slides = [];
                    // حل مشکل تیک خوردن همه: آرایه manual_ids را حتما بساز
                    if((item.type === 'list_horizontal' || item.type === 'grid_categories') && !this.selectedItem.config.manual_ids) {
                        this.selectedItem.config.manual_ids = [];
                    }
                } else {
                    // برای تب‌ها، اگر لینک خالی بود، از link_url کپی کن
                     if(!this.selectedItem.link && this.selectedItem.link_url) {
                        this.selectedItem.link = this.selectedItem.link_url;
                    }
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
                    // اطمینان از اینکه manual_ids یک آرایه است
                    const rawIds = Array.isArray(section.config.manual_ids) ? section.config.manual_ids : [];
                    
                    // تبدیل همه IDها به عدد صحیح برای مقایسه دقیق
                    const ids = rawIds.map(id => parseInt(id)); 
                    
                    // فیلتر کردن محصولات
                    apps = this.allProducts.filter(p => ids.includes(parseInt(p.id)));
                    
                } else {
                    // منطق خودکار (بدون تغییر)
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
            },

            async uploadTabIcon(event) {
                const file = event.target.files[0];
                if(!file) return;

                // استفاده از همان اندپوینت آپلود تصویر که قبلاً داشتیم
                const formData = new FormData();
                formData.append('image', file);
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                try {
                    const res = await fetch('{{ route("admin.layouts.upload_image") }}', {
                        method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': token }
                    });
                    const data = await res.json();
                    
                    if(data.success) {
                        // آپدیت کردن آبجکت انتخابی با لینک جدید و مسیر جدید
                        this.selectedItem.image = data.url;      // برای نمایش
                        this.selectedItem.image_path = data.path; // برای ذخیره در دیتابیس
                    }
                } catch(e) { 
                    alert('خطا در آپلود تصویر');
                }
            },

            // حذف تب از لیست و ارسال درخواست حذف به سرور (اختیاری برای UX بهتر)
            async deleteTab(item) {
                if(!confirm('آیا از حذف این تب مطمئن هستید؟')) return;
                
                // درخواست به سرور برای حذف رکورد
                try {
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    await fetch('/admin/app-tabs/' + item.id, { // فرض بر اینکه روت resource دارید
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                    });
                } catch(e) {
                    console.log('حذف لوکال انجام شد');
                }

                // حذف از لیست جاوااسکریپت
                this.appTabs = this.appTabs.filter(t => t.id !== item.id);
                this.selectedItem = null;
            },

            showTabCreator: false,
                newTabForm: {
                    title: '',
                    link: '',
                    iconType: 'font',
                    icon: 'fas fa-home',
                    image: null,
                    sort_order: null
                },

                // باز کردن پاپ‌آپ روی جایگاه خالی
                openTabCreator(orderIndex) {
                    this.newTabForm = {
                        title: '',
                        link: '', // می‌توانیم به صورت هوشمند slug صفحه جاری را پیشنهاد دهیم
                        iconType: 'font',
                        icon: 'fas fa-home',
                        image: null,
                        sort_order: orderIndex // ترتیب بر اساس جایگاهی که کلیک شده
                    };
                    this.showTabCreator = true;
                },

                // هندل کردن آپلود عکس در فرم جدید
                handleNewTabImage(event) {
                    const file = event.target.files[0];
                    if(file) this.newTabForm.image = file;
                },

                // ارسال فرم به سرور
                async createNewTab() {
                    const formData = new FormData();
                    formData.append('title', this.newTabForm.title);
                    formData.append('link', this.newTabForm.link);
                    formData.append('sort_order', this.newTabForm.sort_order);
                    formData.append('is_active', 1);

                    if (this.newTabForm.iconType === 'font') {
                        formData.append('icon', this.newTabForm.icon);
                    } else if (this.newTabForm.image) {
                        formData.append('image', this.newTabForm.image);
                    }

                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    try {
                        // ارسال به متد store که تغییرش دادیم
                        const res = await fetch('{{ route("admin.app-tabs.store") }}', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json', // مهم: درخواست جیسون
                                'X-CSRF-TOKEN': token
                            },
                            body: formData
                        });

                        const data = await res.json();

                        if (data.success) {
                            // اضافه کردن تب جدید به لیست محلی (برای نمایش آنی)
                            this.appTabs.push({
                                id: data.tab.id,
                                title: data.tab.title,
                                link: data.tab.link,
                                icon: data.tab.icon,
                                image: data.tab.image_url, // از کنترلر می‌آید
                                sort_order: data.tab.sort_order
                            });
                            
                            // مرتب‌سازی دوباره تب‌ها
                            this.appTabs.sort((a, b) => a.sort_order - b.sort_order);

                            this.showTabCreator = false;
                            alert('تب جدید اضافه شد!');
                        } else {
                            alert('خطا در ساخت تب. لطفا ورودی‌ها را چک کنید.');
                        }
                    } catch (e) {
                        console.error(e);
                        alert('خطا در برقراری ارتباط با سرور');
                    }
                },
        }
    }
</script>
@endsection