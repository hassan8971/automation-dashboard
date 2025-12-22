<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicenseViewLog extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id', 'mobile', 'device_info', 'viewed_at'];
    protected $casts = ['viewed_at' => 'datetime'];
}