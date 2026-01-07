<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\Api\V1\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('name_en', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        return ProductResource::collection($query->latest()->paginate(20));
    }

    public function show($id)
    {
        $product = Product::with(['category', 'screenshots', 'subscriptions'])
            ->where('id', $id)
            ->orWhere('slug', $id)
            ->firstOrFail();

        return new ProductResource($product);
    }

    public function install(Request $request, $id)
    {
        $product = Product::with(['subscriptions', 'addons'])->findOrFail($id);
        $user = $request->user();

        // ---------------------------------------------------------
        // PATH A: SUBSCRIPTION ONLY MODE (The "Rayeghan" Checkbox)
        // ---------------------------------------------------------
        if ($product->is_subscription_only) {
            if ($this->checkOwnership($user->id, $id)) {
                return $this->generateDownloadResponse($product);
            }
            return $this->handleStrictSubscriptionCheck($user, $product);
        }

        // ---------------------------------------------------------
        // PATH B: STANDARD / HYBRID MODE
        // ---------------------------------------------------------
        
        // 1. Check Ownership (Did they buy it? Even for $0)
        if ($this->checkOwnership($user->id, $id)) {
            return $this->generateDownloadResponse($product);
        }

        // 2. CHECK ADD-ONS (Priority 1)
        // If product has add-ons, we use Add-on logic strictly.
        if ($product->addons->isNotEmpty()) {
            
            $addonResponse = $this->handleTieredAddonCheck($user, $product);

            if ($addonResponse['allowed']) {
                return $this->generateDownloadResponse($product);
            }

            // If allowed but price > 0 (Arcade Price)
            if (isset($addonResponse['price'])) {
                return response()->json([
                    'message' => 'برای دانلود این برنامه باید آن را خریداری کنید (قیمت ویژه مشترکین آرکید).',
                    'action' => 'payment_required',
                    'price' => $addonResponse['price']
                ], 403);
            }
        }
        // 3. CHECK SUBSCRIPTIONS (Priority 2 - Mutually Exclusive per your request)
        // Only runs if no add-ons are defined for this product
        elseif ($product->subscriptions->isNotEmpty() && $user->activeSubscription) {
             
             $subResponse = $this->handleTieredSubscriptionCheck($user, $product);
             
             if ($subResponse['allowed']) {
                 return $this->generateDownloadResponse($product);
             }

             if (isset($subResponse['price'])) {
                 return response()->json([
                    'message' => 'برای دانلود این برنامه باید آن را خریداری کنید (قیمت ویژه مشترکین).',
                    'action' => 'payment_required',
                    'price' => $subResponse['price']
                ], 403);
             }
        }

        // 4. Public Price Check
        if ($product->price == 0) {
            return response()->json([
                'message' => 'اپلیکیشن رایگان است و کاربر میتواند آن را دریافت کند.',
                'action' => 'payment_required', 
                'price' => 0,
            ], 403);
        }

        // 5. Paid App
        return response()->json([
            'message' => 'برای دانلود این برنامه باید آن را خریداری کنید.',
            'action' => 'payment_required',
            'price' => $product->price
        ], 403);
    }

    public function buy(Request $request, $id)
    {
        $product = Product::with(['subscriptions', 'addons'])->findOrFail($id);
        $user = $request->user();

        // 🚫 Block buying if it's "Subscription Only"
        if ($product->is_subscription_only) {
            return response()->json([
                'message' => 'این محصول قابل خرید نیست و فقط با اشتراک در دسترس است.',
                'action' => 'upgrade_required'
            ], 403);
        }

        // 1. Check Duplicate
        if ($this->checkOwnership($user->id, $id)) {
            return response()->json(['success' => true, 'message' => 'شما قبلاً این محصول را دارید.']);
        }

        // 2. Calculate Price & Determine Method
        $finalPrice = $product->price;
        $purchaseMethod = 'normal'; // Default method

        // --- ADD-ON LOGIC ---
        if ($product->addons->isNotEmpty()) {
            $allowedAddonSlugs = $product->addons->pluck('slug')->toArray();
            $userAddonSlugs = $user->activeAddons()->with('addon')->get()->pluck('addon.slug')->toArray();
            
            $matchingSlugs = array_intersect($allowedAddonSlugs, $userAddonSlugs);

            if (in_array('arcade', $matchingSlugs)) {
                $finalPrice = $product->price_arcade;
                $purchaseMethod = 'addon'; // Purchased via Addon pricing
            }
        }
        // --- SUBSCRIPTION LOGIC ---
        elseif ($product->subscriptions->isNotEmpty() && $user->activeSubscription) {
            $allowedSlugs = $product->subscriptions->pluck('slug')->toArray();
            $activePlan = $user->activeSubscription->plan; 

            if ($activePlan && in_array($activePlan->slug, $allowedSlugs)) {
                $planSlug = $activePlan->slug;
                
                $priceCol = match($planSlug) {
                    'sibaneh_plus' => 'price_sibaneh_plus',
                    'sibaneh_pro'  => 'price_sibaneh_pro',
                    default        => 'price_sibaneh'
                };
                
                $finalPrice = $product->{$priceCol};
                $purchaseMethod = 'subscription'; // Purchased via Subscription pricing
            }
        }

        // 3. RESTRICTION: One Normal Price App Limit
        if ($purchaseMethod === 'normal') {
            $existingNormalCount = DB::table('user_apps')
                ->where('user_id', $user->id)
                ->where('purchase_method', 'normal')
                ->count();

            if ($existingNormalCount >= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'شما قبلاً یک برنامه با تعرفه عادی خریده‌اید. برای خرید برنامه‌های بیشتر لطفاً اشتراک تهیه کنید.',
                    'action' => 'upgrade_required'
                ], 403);
            }
        }

        // 4. Process Free "Purchase"
        if ($finalPrice == 0) {
            $this->recordOwnership($user->id, $id, 0, $purchaseMethod);
            return response()->json(['success' => true, 'message' => 'محصول به رایگان به حساب شما اضافه شد.']);
        }

        // 5. Wallet Check
        if ($user->wallet->balance < $finalPrice) {
            return response()->json([
                'message' => 'موجودی کیف پول کافی نیست.',
                'required' => $finalPrice,
                'balance' => $user->wallet->balance
            ], 402);
        }

        // 6. Transaction
        try {
            DB::beginTransaction();

            $user->wallet->withdraw(
                $finalPrice,
                'withdraw', 
                "خرید اپلیکیشن: {$product->title}"
            );

            // Pass purchase_method to the recorder
            $this->recordOwnership($user->id, $id, $finalPrice, $purchaseMethod);

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'خرید با موفقیت انجام شد.',
                'remaining_balance' => $user->wallet->fresh()->balance
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در تراکنش: ' . $e->getMessage()], 500);
        }
    }

    // --- NEW HELPER FOR ADD-ONS ---
    private function handleTieredAddonCheck($user, $product) {
        $allowedAddonSlugs = $product->addons->pluck('slug')->toArray();
        
        // Eager load addons to avoid N+1
        $userActiveAddons = $user->activeAddons()->with('addon')->get();

        // Check if user has ANY of the allowed addons
        $matchingUserAddon = $userActiveAddons->first(function($ua) use ($allowedAddonSlugs) {
            return in_array($ua->addon->slug, $allowedAddonSlugs);
        });

        // If user doesn't own any allowed addon
        if (!$matchingUserAddon) {
            return ['allowed' => false];
        }

        // Check specific price logic (Arcade)
        if ($matchingUserAddon->addon->slug === 'arcade') {
            $price = $product->price_arcade;

            // If price > 0, they must buy at discounted rate
            if ($price > 0) {
                return [
                    'allowed' => false,
                    'price' => $price
                ];
            }
            
            // If price is 0, they can download
            return ['allowed' => true];
        }

        // Default behavior for other addons (if any): Assume free access if owned
        return ['allowed' => true];
    }

    private function checkOwnership($userId, $productId) {
        return \DB::table('user_apps')
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();
    }

    private function handleStrictSubscriptionCheck($user, $product) {
        $userSub = $user->activeSubscription()->with('subscription')->first();
        $allowedSlugs = $product->subscriptions->pluck('slug')->toArray();

        // No Sub?
        if (!$userSub || !$userSub->subscription) {
            return response()->json([
                'message' => 'این برنامه رایگان مخصوص مشترکین است. لطفاً اشتراک تهیه کنید.',
                'action' => 'payment_required'
            ], 403);
        }

        // Wrong Sub?
        if (!empty($allowedSlugs) && !in_array($userSub->subscription->slug, $allowedSlugs)) {
             return response()->json([
                'message' => 'اشتراک فعلی شما دسترسی به این برنامه را ندارد.',
                'action' => 'upgrade_required'
            ], 403);
        }

        // Pass! (Prices ignored)
        return $this->generateDownloadResponse($product);
    }

    private function handleTieredSubscriptionCheck($user, $product) {
        $userSub = $user->activeSubscription()->with('subscription')->first();
        $allowedSlugs = $product->subscriptions->pluck('slug')->toArray();
        
        // If user has no sub or sub object is missing
        if (!$userSub || !$userSub->subscription) return ['allowed' => false];
        
        // If sub is not in the allowed list for this product
        if (!in_array($userSub->subscription->slug, $allowedSlugs)) return ['allowed' => false];
        
        // Determine the price column based on the plan
        $priceCol = match($userSub->subscription->slug) {
            'sibaneh_plus' => 'price_sibaneh_plus',
            'sibaneh_pro'  => 'price_sibaneh_pro',
            default        => 'price_sibaneh',
        };
        
        $price = $product->{$priceCol};

        // If price is greater than 0, return false BUT include the price
        if ($price > 0) {
            return [
                'allowed' => false, 
                'price' => $price // <--- Return the discounted price
            ]; 
        }
        
        return ['allowed' => true];
    }

    private function recordOwnership($userId, $productId, $price)
    {
        DB::table('user_apps')->insert([
            'user_id' => $userId,
            'product_id' => $productId,
            'price_paid' => $price,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function generateDownloadResponse($product)
    {
        $product->increment('download_count');

        $downloadUrl = match(true) {
            $product->type_pwa => $product->pwa_url,
            $product->type_internal => $product->internal_url, 
            default => $product->native_appstore_url,
        };

        return response()->json([
            'success' => true,
            'url' => $downloadUrl,
            'install_method' => $product->type_pwa ? 'pwa' : 'native'
        ]);
    }
}