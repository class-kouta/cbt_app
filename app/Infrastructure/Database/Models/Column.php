<?php

namespace App\Infrastructure\Database\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Column extends Model
{
    use HasFactory;

    protected $fillable = [
        'situation',
        'mood',
        'automatic_thought',
        'evidence',
        'counter_evidence',
        'adaptive_thought',
        'current_mood',
        'notes',
    ];
}
