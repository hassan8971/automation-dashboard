<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Gift extends Model
{
    protected $fillable = [
        'title',
        'type',
        'rewardable_id',
        'rewardable_type',
        'payload',
        'generated_amount',
        'generated_service_type',
        'generated_access_level',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * The item being given as a gift (e.g., A SubscriptionPlan)
     */
    public function rewardable(): MorphTo
    {
        return $this->morphTo();
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(SubscriptionPlan::class, 'gift_id');
    }
}