<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany; 
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'mobile',
        'email',
        'zip_code',
        'province',
        'city',
        'address',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * A user can have many saved addresses.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Get all reviews written by this user.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Check if the user has purchased a specific product.
     * (بررسی می‌کند آیا کاربر این محصول را خریده است یا نه)
     */
    public function hasPurchased(int $productId): bool
    {
        // 1. Get all product variant IDs for the given product
        $variantIds = Product::find($productId)->variants->pluck('id');

        // 2. Check if this user has any 'delivered' or 'shipped' orders
        // that contain any of those variant IDs.
        return $this->orders()
            ->whereIn('status', ['delivered', 'shipped', 'completed']) // یا هر وضعیتی که نشانه خرید قطعی است
            ->whereHas('items', function ($query) use ($variantIds) {
                $query->whereIn('product_variant_id', $variantIds);
            })
            ->exists();
    }

    public function devices(): HasMany
    {
        return $this->hasMany(UserDevice::class);
    }

    public function installedApps(): HasMany
    {
        return $this->hasMany(UserInstalledApp::class);
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(UserRedemption::class);
    }

    public function licenseLogs(): HasMany
    {
        return $this->hasMany(LicenseViewLog::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Get the current active subscription (if any).
     */
    public function activeSubscription(): HasOne
    {
        return $this->hasOne(UserSubscription::class)
                    ->where('is_active', true)
                    ->where('expires_at', '>', now())
                    ->latestOfMany();
    }

    /**
     * --- DYNAMIC STATUS ACCESSOR ---
     * values: Guest, Visitor, Member, Prospect, Subscriber, Expiring, Expired
     */
    public function getStatusAttribute(): string
    {
        // 1. Subscriber & Expiring Logic
        $subscription = $this->activeSubscription;

        if ($subscription) {
            // Check if expiring soon (e.g., within 7 days)
            if ($subscription->expires_at->diffInDays(now()) <= 7) {
                return 'Expiring';
            }
            return 'Subscriber';
        }

        // 2. Expired Logic (Has a past subscription but no active one)
        $hasExpiredSubscription = $this->subscriptions()
                                       ->where('expires_at', '<', now())
                                       ->exists();
        if ($hasExpiredSubscription) {
            return 'Expired';
        }

        // 3. Prospect (Signed up, went to payment, but didn't complete)
        // logic: Has 'pending' orders related to subscriptions
        // Assuming Order model has 'type' or we check order items
        $hasPendingOrder = $this->orders()
                                ->where('status', 'pending') // or 'awaiting_payment'
                                ->exists();
        if ($hasPendingOrder) {
            return 'Prospect';
        }

        // 4. Member (Signed up fully: Name & Email exist)
        if (!empty($this->name) && !empty($this->email)) {
            return 'Member';
        }

        // 5. Visitor (Only Mobile exists, profile incomplete)
        if (!empty($this->mobile)) {
            return 'Visitor';
        }

        // 6. Guest (No DB record usually, but fallback if user instance exists with no data)
        return 'Guest';
    }
    
    /**
     * Helper to log a license view
     */
    public function logLicenseView($deviceInfo = null)
    {
        $this->increment('license_view_count');
        
        $this->licenseLogs()->create([
            'mobile' => $this->mobile,
            'device_info' => $deviceInfo,
            'viewed_at' => now(),
        ]);
    }

}