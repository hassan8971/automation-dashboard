<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// Import the base Authenticatable model
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;


class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * Set the guard to 'admin' (good practice for multi-auth)
     *
     * @var string
     */
    protected $guard = 'admin';



    protected $fillable = [
      'name', 'username', 'email', 'mobile', 'last_name',
    'nickname',
    'use_nickname',
    'profile_photo_path',
    'dashboard_banner_path',
    ];

    protected $casts = [
        'use_nickname' => 'boolean', // مهم برای چک‌باکس
    ];

      /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
    ];


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all the products created by this admin.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
