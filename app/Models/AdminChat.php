<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;

class AdminChat extends Model
{
    use HasFactory;
    protected $fillable = ['admin_id', 'message'];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id'); // یا مدل Admin
    }
}
