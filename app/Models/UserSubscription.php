<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserSubscription extends Model
{
    protected $fillable = [
        'user_id', 
        'subscription_id', 
        'starts_at', 
        'expires_at', 
        'status', 
        'price_paid'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function plan()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper to check if still valid
    public function isValid()
    {
        return $this->status === 'active' && $this->expires_at->isFuture();
    }
    public function subscription()
    {
        // Assuming your plans table is 'subscriptions'
        return $this->belongsTo(Subscription::class);
    }
}