<?php

namespace App\Infrastructure\Database\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthyAdultModeImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
    ];
}
