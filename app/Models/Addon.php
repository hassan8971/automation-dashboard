<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Addon extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'duration_in_days',
        'supported_apps',
        'description',
        'is_active',
    ];

    protected $casts = [
        'supported_apps' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get all gifts attached to this Add-on (As a Trigger).
     */
    public function gifts(): MorphMany
    {
        return $this->morphMany(Gift::class, 'triggerable');
    }
}