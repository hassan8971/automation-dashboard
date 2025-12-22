<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'price', 'duration_in_days','description', // اضافه شد
        'gift_id', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all gifts attached to this subscription plan.
     */
    public function gift(): BelongsTo
    {
        return $this->belongsTo(Gift::class);
    }
}
