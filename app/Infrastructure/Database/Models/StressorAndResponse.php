<?php

namespace App\Infrastructure\Database\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StressorAndResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'stressor',
        'cognition',
        'mood',
        'body_reaction',
        'behavior',
        'stimulated_schemas',
    ];

    protected $casts = [
        'stimulated_schemas' => 'array',
    ];
}
