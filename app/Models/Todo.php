<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    protected $fillable = ['admin_id', 'title', 'description', 'due_date', 'is_completed'];

    protected $casts = [
        'is_completed' => 'boolean',
        'due_date' => 'datetime',
    ];
}
