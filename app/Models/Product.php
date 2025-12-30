<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'is_stable' => 'boolean',
        'type_pwa' => 'boolean',
        'type_adhoc' => 'boolean',
        'type_internal' => 'boolean',
        'type_appstore' => 'boolean',
        'app_updated_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function screenshots()
    {
        return $this->hasMany(ProductScreenshot::class)->orderBy('sort_order');
    }

    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class, 'product_subscription');
    }

    public function addons()
    {
        return $this->belongsToMany(Addon::class, 'product_addon');
    }
}