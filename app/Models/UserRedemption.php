<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRedemption extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id', 'code', 'used_at'];
    protected $casts = ['used_at' => 'datetime'];
}