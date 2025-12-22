<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    protected $fillable = ['user_id', 'subscription_plan_id', 'started_at', 'expires_at', 'is_active'];
    protected $casts = [
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }
}