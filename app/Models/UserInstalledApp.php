<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInstalledApp extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id', 'app_name', 'bundle_id', 'downloaded_at'];
    protected $casts = ['downloaded_at' => 'datetime'];
}