<?php

namespace App\Infrastructure\Database\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chronology extends Model
{
    use HasFactory;

    protected $fillable = [
        'when_period',
        'environment_event',
        'experience_feeling',
        'sentiment_type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
