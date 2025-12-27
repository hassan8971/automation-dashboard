<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RedeemCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'amount',
        'service_type',
        'usage_limit',
        'used_count',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // لیست سرویس‌های پشتیبانی شده
    const SERVICES = [
        'all' => 'همه سرویس‌ها',
        'appstore' => 'اپ استور',
        'macstore' => 'مک استور',
        'sibaneh_code' => 'سیبانه کد',
        'sibaneh_business' => 'سیبانه بیزینس',
        'sibaneh_prime' => 'سیبانه پرایم',
    ];

    // اسکوپ برای کدهای قابل استفاده
    public function scopeValid($query)
    {
        return $query->where('is_active', true)
                     ->whereColumn('used_count', '<', 'usage_limit')
                     ->where(function ($q) {
                         $q->whereNull('expires_at')
                           ->orWhere('expires_at', '>', now());
                     });
    }
}