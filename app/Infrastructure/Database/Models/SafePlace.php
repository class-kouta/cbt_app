<?php

namespace App\Infrastructure\Database\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SafePlace extends Model
{
    use HasFactory;

    protected $fillable = [
        'safe_image',
        'safe_something',
    ];
}
