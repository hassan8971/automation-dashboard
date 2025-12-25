<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddon extends Model
{
    protected $fillable = [
        'user_id', 
        'addon_id', 
        'price_paid', 
        'status', 
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function addon()
    {
        return $this->belongsTo(Addon::class);
    }
}