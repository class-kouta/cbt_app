<?php

namespace App\Infrastructure\Database\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'difficulty_id',
        'content',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];
}

