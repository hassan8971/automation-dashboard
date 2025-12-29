<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subscription;
use App\Models\ProductScreenshot;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Services\SibanehAiService;

class ProductController extends Controller
{
    protected $ai;

    // تزریق سرویس در کانستراکتور
    public function __construct(SibanehAiService $ai)
    {
        $this->ai = $ai;
    }

    public function index()
    {
        $products = Product::with('category')->latest()->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $subscriptions = Subscription::where('is_active', true)->get();
        return view('admin.products.create', compact('categories', 'subscriptions'));
    }

    public function store(Request $request)
    {
        $this->saveProduct($request, new Product());
        return redirect()->route('admin.products.index')->with('success', 'اپلیکیشن با موفقیت ایجاد شد.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $subscriptions = Subscription::where('is_active', true)->get();
        return view('admin.products.edit', compact('product', 'categories', 'subscriptions'));
    }

    public function update(Request $request, Product $product)
    {
        $this->saveProduct($request, $product);
        return redirect()->route('admin.products.index')->with('success', 'اپلیکیشن ویرایش شد.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'اپلیکیشن حذف شد.');
    }

    /**
     * متد جدید برای دریافت اطلاعات از آیتونز
     */
public function fetchItunes(Request $request)
    {
        $id = $request->input('id');
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'لطفا شناسه App ID را وارد کنید.']);
        }

        try {
            // 1. دریافت اطلاعات از اپ‌استور
            $response = Http::withoutVerifying()->timeout(15)->get("https://itunes.apple.com/lookup", [
                'id' => $id,
                'entity' => 'software',
                'country' => 'us'
            ]);

            if ($response->failed()) {
                Log::error('Apple API Error: ' . $response->body());
                return response()->json(['success' => false, 'message' => 'خطا در اتصال به اپ‌استور.']);
            }

            $data = $response->json();
            if (empty($data['results']) || !is_array($data['results'])) {
                return response()->json(['success' => false, 'message' => 'اپلیکیشنی با این شناسه یافت نشد.']);
            }

            $app = $data['results'][0];

            // 2. آماده‌سازی فیلدهای عادی
            $sizeMB = isset($app['fileSizeBytes']) ? round($app['fileSizeBytes'] / 1024 / 1024, 1) . ' MB' : '';
            $categoryId = null;
            if (isset($app['primaryGenreName'])) {
                $category = Category::firstOrCreate(
                    ['name' => $app['primaryGenreName']],
                    ['slug' => Str::slug($app['primaryGenreName'])]
                );
                $categoryId = $category->id;
            }
            $screenshots = array_merge($app['screenshotUrls'] ?? [], $app['ipadScreenshotUrls'] ?? []);

            // 3. ترجمه توضیحات (همراه با لاگ و خطایابی)
            $descEn = $app['description'] ?? '';
            $descFa = '';

            $notesEn = $app['releaseNotes'] ?? '';
            $notesFa = '';


            $translationError = null;

            if ($descEn) {
                try {
                    Log::info("Starting AI Translation for App ID: $id");
                    
                    $prompt = "متن زیر توضیحات کامل یک اپلیکیشن است. لطفاً تمام متن را بدون کم و کاست و بدون خلاصه کردن، به فارسی روان و جذاب ترجمه کن. ساختار پاراگراف‌ها حفظ شود:\n\n" . $descEn;
                    
                    // فراخوانی سرویس هوش مصنوعی
                    $descFa = $this->ai->generateText($prompt);

                    if (empty($descFa)) {
                        Log::warning("AI returned empty translation for App ID: $id");
                        $translationError = 'هوش مصنوعی پاسخی نداد (خروجی خالی). لاگ‌ها را بررسی کنید.';
                    } else {
                        Log::info("AI Translation Successful for App ID: $id");
                    }

                } catch (\Exception $e) {
                    Log::error("AI Translation Exception: " . $e->getMessage());
                    $translationError = 'خطا در سرویس ترجمه: ' . $e->getMessage();
                }
            } else {
                $translationError = 'توضیحات انگلیسی برای ترجمه یافت نشد.';
            }

            if ($notesEn) {
                try {
                    // پرامپت مخصوص تغییرات نسخه
                    $prompt = "متن زیر لیست تغییرات (Release Notes) یک اپلیکیشن است. آن را به فارسی روان ترجمه کن. لیست‌بندی و خطوط جدید را حفظ کن. فقط و فقط به فارسی ترجمه کن:\n\n" . $notesEn;
                    
                    $notesFa = $this->ai->generateText($prompt);
                    
                } catch (\Exception $e) {
                    Log::error("AI Release Notes Translation Error: " . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'title' => $app['trackName'] ?? '',
                    'name_en' => $app['trackName'] ?? '',
                    'bundle_id' => $app['bundleId'] ?? '',
                    'version' => $app['version'] ?? '',
                    'price_appstore' => $app['price'] ?? 0,
                    'size' => $sizeMB,
                    'seller' => $app['artistName'] ?? '',
                    'seller_website' => $app['sellerUrl'] ?? '',
                    'description' => $descEn,
        
                    'description_fa' => $descFa,

                    'release_notes' => $notesEn,
                    'release_notes_fa' => $notesFa,

                    'translation_error' => $translationError,
                    
                    
                    'age_rating' => $app['contentAdvisoryRating'] ?? '',
                    'appstore_link' => $app['trackViewUrl'] ?? '',
                    'category_id' => $categoryId,
                    'icon_url' => $app['artworkUrl512'] ?? '',
                    'screenshots_urls' => $screenshots,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Fetch Itunes Exception: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'خطای سرور: ' . $e->getMessage()]);
        }
    }

    private function saveProduct(Request $request, Product $product)
    {
        // تمیزکاری قیمت‌ها
        $prices = ['price_sibaneh', 'price_appstore', 'pwa_price', 'internal_price'];
        foreach ($prices as $field) {
            if ($request->has($field)) {
                $val = str_replace(',', '', $request->input($field));
                if ($val === '') $val = null;
                $request->merge([$field => $val]);
            }
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'name_fa' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'price' => 'nullable|numeric',
            'price_sibaneh' => 'nullable|numeric',
            'price_sibaneh_plus' => 'nullable|numeric',
            'price_sibaneh_pro' => 'nullable|numeric',
            'price_appstore' => 'nullable|numeric',
            'bundle_id' => 'nullable|string',
            'version' => 'nullable|string',
            'fetched_icon_url' => 'nullable|url',
            'fetched_screenshots' => 'nullable|array',
            'fetched_screenshots.*' => 'url',
            // بقیه ولیدیشن‌ها...
        ]);

        // ذخیره فیلدهای ساده
        $product->title = $request->title;
        $product->name_fa = $request->name_fa;
        $product->name_en = $request->name_en;
        if (!$product->exists || $product->isDirty('title')) {
            $product->slug = Str::slug($request->title . '-' . Str::random(4));
        }
        $product->category_id = $request->category_id;
        $product->price = $request->price ?? 0;
        $product->price_sibaneh = $request->price_sibaneh ?? 0;
        $product->price_sibaneh_plus = $request->price_sibaneh_plus ?? 0;
        $product->price_sibaneh_pro = $request->price_sibaneh_pro ?? 0;
        $product->price_appstore = $request->price_appstore ?? 0;
        $product->bundle_id = $request->bundle_id;
        $product->version = $request->version;
        $product->size = $request->size;
        $product->seller = $request->seller;
        $product->seller_website = $request->seller_website;
        $product->is_stable = $request->has('is_stable');
        $product->availability = $request->availability ?? 'available';
        $product->description = $request->description;
        $product->description_fa = $request->description_fa;
        $product->release_notes = $request->release_notes;
        $product->release_notes_fa = $request->release_notes_fa;
        $product->how_to_install_url = $request->how_to_install_url;
        $product->appstore_link = $request->appstore_link;
        $product->age_rating = $request->age_rating;
        $product->app_updated_at = $request->app_updated_at;

        // ذخیره فایل‌ها: اولویت با فایل آپلودی است، بعد URL فچ شده
        if ($request->hasFile('icon')) {
            $product->icon_path = $request->file('icon')->store('products/icons', 'public');
        } elseif ($request->filled('fetched_icon_url') && !$product->icon_path) {
            // دانلود آیکون از URL
            try {
                $contents = file_get_contents($request->fetched_icon_url);
                if ($contents) {
                    $filename = 'fetched_icon_' . Str::random(10) . '.jpg';
                    Storage::disk('public')->put('products/icons/' . $filename, $contents);
                    $product->icon_path = 'products/icons/' . $filename;
                }
            } catch (\Exception $e) {
                // اگر دانلود عکس فیل شد، سخت نگیر
                Log::warning('Failed to download icon: ' . $e->getMessage());
            }
        }

        if ($request->hasFile('banner_detail')) {
            $product->banner_detail_path = $request->file('banner_detail')->store('products/banners', 'public');
        }
        if ($request->hasFile('banner_vitrin')) {
            $product->banner_vitrin_path = $request->file('banner_vitrin')->store('products/banners', 'public');
        }

        // Publish Settings
        $product->type_pwa = $request->has('type_pwa');
        $product->pwa_price = $request->pwa_price;
        $product->pwa_url = $request->pwa_url;
        $product->type_internal = $request->has('type_internal');
        $product->internal_price = $request->internal_price;
        $product->internal_url = $request->internal_url;
        $product->type_appstore = $request->has('type_appstore');
        $product->native_appstore_url = $request->native_appstore_url;
        $product->native_appstore_username = $request->native_appstore_username;
        $product->native_appstore_password = $request->native_appstore_password;
        
        $product->save();

        if ($request->has('subscriptions')) {
            $product->subscriptions()->sync($request->subscriptions);
        }

        // Screenshots Upload
        if ($request->hasFile('screenshots')) {
            foreach ($request->file('screenshots') as $file) {
                $path = $file->store('products/screenshots', 'public');
                ProductScreenshot::create(['product_id' => $product->id, 'image_path' => $path]);
            }
        }
        // Fetched Screenshots
        if ($request->filled('fetched_screenshots')) {
            foreach ($request->fetched_screenshots as $url) {
                try {
                    $contents = file_get_contents($url);
                    if ($contents) {
                        $filename = 'fetched_screen_' . Str::random(10) . '.jpg';
                        Storage::disk('public')->put('products/screenshots/' . $filename, $contents);
                        ProductScreenshot::create([
                            'product_id' => $product->id, 
                            'image_path' => 'products/screenshots/' . $filename
                        ]);
                    }
                } catch (\Exception $e) { continue; }
            }
        }
    }
}