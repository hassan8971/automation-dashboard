<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user('sanctum'); // Check if user is logged in

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'name_en' => $this->name_en,
            'name_fa' => $this->name_fa,
            
            // Image URLs
            'icon_url' => $this->icon_path ? url(Storage::url($this->icon_path)) : null,
            'banner_url' => $this->banner_detail_path ? url(Storage::url($this->banner_detail_path)) : null,
            
            // Metadata
            'version' => $this->version,
            'size' => $this->size,
            'age_rating' => $this->age_rating,
            'category' => $this->category ? $this->category->name : null,
            
            // Description (You might want to return HTML or clean text)
            'description' => $this->description_fa ?? $this->description,
            'release_notes' => $this->release_notes_fa ?? $this->release_notes,
            
            // Pricing Logic for Frontend UI
            'appstore_price' => $this->price_appstore > 0 ? number_format($this->price_appstore) . ' تومان' : 'رایگان',
            'is_free' => $this->price == 0,
            
            // User Access Status (The Hybrid Logic)
            'user_access' => $this->checkUserAccess($user),
            
            // Screenshots
            'screenshots' => $this->screenshots->map(function($screen) {
                return url(Storage::url($screen->image_path));
            }),
        ];
    }

    /**
     * Determine if the user can download this app
     */
    private function checkUserAccess($user)
    {
        // 1. GUEST CHECK
        if (!$user) {
            return [
                'can_download' => false,
                'action' => 'login_required',
                'final_price' => $this->price,
                'message' => 'برای مشاهده وضعیت دانلود وارد حساب کاربری شوید.'
            ];
        }

        // 2. OWNERSHIP CHECK (Single Purchase)
        $hasPurchased = \DB::table('user_apps')
            ->where('user_id', $user->id)
            ->where('product_id', $this->id)
            ->exists();

        if ($hasPurchased) {
            return [
                'can_download' => true,
                'action' => 'download',
                'status' => 'purchased',
                'message' => 'شما مالک این محصول هستید.'
            ];
        }

        // 3. SUBSCRIPTION LOGIC
        // We need to know:
        // A) Does the user have a subscription?
        // B) Is that subscription ALLOWED for this product?
        
        $activeSub = $user->activeSubscription; 
        
        // Get IDs of subscriptions allowed for this product
        // (Make sure to use with('subscriptions') in Controller)
        $allowedSubIds = $this->subscriptions->pluck('id')->toArray();
        $isRestrictedToSubs = !empty($allowedSubIds);

        // Check if user has a valid subscription for THIS app
        if ($activeSub && in_array($activeSub->id, $allowedSubIds)) {
            
            $planSlug = $activeSub->slug ?? 'sibaneh'; 
            
            $priceColumn = match($planSlug) {
                'sibaneh_plus' => 'price_sibaneh_plus',
                'sibaneh_pro'  => 'price_sibaneh_pro',
                default        => 'price_sibaneh',
            };

            $planPrice = $this->{$priceColumn};

            if ($planPrice == 0) {
                return [
                    'can_download' => true,
                    'action' => 'download',
                    'status' => 'subscription_included',
                    'plan' => $planSlug,
                    'message' => 'رایگان برای مشترکین ' . $activeSub->name // e.g. "Free for Sibaneh Plus users"
                ];
            } else {
                return [
                    'can_download' => false,
                    'action' => 'payment_required',
                    'final_price' => $planPrice,
                    'original_price' => $this->price,
                    'status' => 'subscription_discount',
                    'plan' => $planSlug,
                    'message' => 'خرید با تخفیف ویژه مشترکین'
                ];
            }
        }

        // 4. EXCLUSIVITY CHECK (This fixes your issue)
        // If the app is restricted to specific subscriptions, and the user didn't pass check #3...
        // ...then they CANNOT download it for free, even if base price is 0.
        if ($isRestrictedToSubs) {
            // Get names of allowed subscriptions for display
            $allowedNames = $this->subscriptions->pluck('title')->join('، ');
            
            return [
                'can_download' => false,
                'action' => 'subscription_required',
                'final_price' => $this->price, // Or 0 if it's purely sub-only
                'status' => 'locked_by_subscription',
                'message' => "این برنامه مخصوص مشترکین $allowedNames است."
            ];
        }

        // 5. PUBLIC FREE CHECK
        // If we are here, it means the app has NO subscription restrictions.
        // It is a truly public app.
        if ($this->price == 0) {
            return [
                'can_download' => true,
                'action' => 'download',
                'status' => 'free',
                'message' => 'رایگان'
            ];
        }

        // 6. FULL PRICE PAYMENT
        return [
            'can_download' => false,
            'action' => 'payment_required',
            'final_price' => $this->price,
            'status' => 'full_price',
            'message' => 'نیازمند خرید یا اشتراک'
        ];
    }
}