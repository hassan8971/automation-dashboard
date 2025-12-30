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
        $product = Product::with('subscriptions')->findOrFail($id);
        $user = $request->user();

        // ---------------------------------------------------------
        // PATH A: SUBSCRIPTION ONLY MODE (The "Rayeghan" Checkbox)
        // ---------------------------------------------------------
        if ($product->is_subscription_only) {
            // 1. Check Ownership (Just in case they bought it before it became sub-only)
            if ($this->checkOwnership($user->id, $id)) {
                return $this->generateDownloadResponse($product);
            }

            // 2. Subscription Check
            // We ignore all prices here. If they have the sub, they get in.
            return $this->handleStrictSubscriptionCheck($user, $product);
        }

        // ---------------------------------------------------------
        // PATH B: STANDARD / HYBRID MODE
        // ---------------------------------------------------------
        
        // 1. Check Ownership (Did they buy it? Even for $0)
        if ($this->checkOwnership($user->id, $id)) {
            return $this->generateDownloadResponse($product);
        }

        // 2. Check Subscription (With Tier Pricing)
        // Even if not "Sub Only", subs might give discounts.
        if ($product->subscriptions->isNotEmpty() && $user->activeSubscription) {
             // We attempt to grant access via sub (considering tier prices)
             $subResponse = $this->handleTieredSubscriptionCheck($user, $product);
             if ($subResponse['allowed']) {
                 return $this->generateDownloadResponse($product);
             }
             // If not allowed via sub (e.g. wrong plan), we fall through to Price Check
        }

        // 3. Public Price Check
        // If Price is 0 -> It's "Public Free". User must "Buy" it for $0 first.
        if ($product->price == 0) {
            return response()->json([
                'message' => 'اپلیکیشن رایگان است و کاربر میتواند آن را دریافت کند.',
                'action' => 'payment_required', // Frontend triggers /buy for 0
                'price' => 0,
            ], 403);
        }

        // 4. Paid App
        return response()->json([
            'message' => 'برای دانلود این برنامه باید آن را خریداری کنید.',
            'action' => 'payment_required',
            'price' => $product->price
        ], 403);
    }

    public function buy(Request $request, $id)
    {
        $product = Product::findOrFail($id);
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

        // 2. Calculate Price
        $finalPrice = $product->price;

        // Check for Subscription Discounts
        if ($user->activeSubscription) {
            $allowedSlugs = $product->subscriptions->pluck('slug')->toArray();
            
            // Fixed: Now using Slugs here too for consistency
            if (in_array($user->activeSubscription->slug, $allowedSlugs)) {
                $planSlug = $user->activeSubscription->slug;
                $priceCol = match($planSlug) {
                    'sibaneh_plus' => 'price_sibaneh_plus',
                    'sibaneh_pro' => 'price_sibaneh_pro',
                    default => 'price_sibaneh'
                };
                $finalPrice = $product->{$priceCol};
            }
        }

        // 3. Process Free "Purchase"
        if ($finalPrice == 0) {
            $this->recordOwnership($user->id, $id, 0);
            return response()->json(['success' => true, 'message' => 'محصول به رایگان به حساب شما اضافه شد.']);
        }

        // 4. Wallet Check
        if ($user->wallet->balance < $finalPrice) {
            return response()->json([
                'message' => 'موجودی کیف پول کافی نیست.',
                'required' => $finalPrice,
                'balance' => $user->wallet->balance
            ], 402);
        }

        // 5. Transaction
        try {
            DB::beginTransaction();

            $user->wallet->withdraw(
                $finalPrice,
                'withdraw', 
                "خرید اپلیکیشن: {$product->title}"
            );

            $this->recordOwnership($user->id, $id, $finalPrice);

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
        // Your previous logic checking slugs and tier prices...
        // Return ['allowed' => true] or ['allowed' => false]
        // I kept this brief to save space, but copy your logic from the previous step here.
        
        $userSub = $user->activeSubscription()->with('subscription')->first();
        $allowedSlugs = $product->subscriptions->pluck('slug')->toArray();
        
        if (!$userSub || !$userSub->subscription) return ['allowed' => false];
        
        if (!in_array($userSub->subscription->slug, $allowedSlugs)) return ['allowed' => false];
        
        // Check price
        $priceCol = match($userSub->subscription->slug) {
            'sibaneh_plus' => 'price_sibaneh_plus',
            'sibaneh_pro'  => 'price_sibaneh_pro',
            default        => 'price_sibaneh',
        };
        
        if ($product->{$priceCol} > 0) return ['allowed' => false]; // Needs payment
        
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