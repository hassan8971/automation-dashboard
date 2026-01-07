<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppTab extends Model
{
    protected $table = 'app_tabs'; // نام جدول جدید

    protected $fillable = [
        'title',
        'link',
        'icon',
        'image_path',
        'sort_order',
        'is_active',
    ];
}