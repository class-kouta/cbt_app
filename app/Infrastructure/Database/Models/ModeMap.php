<?php

namespace App\Infrastructure\Database\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModeMap extends Model
{
    use HasFactory;

    protected $fillable = [
        'wounded_child_mode',
        'hurtful_adult_mode',
        'unacceptable_coping_mode',
        'healthy_happy_child_mode',
        'healthy_adult_mode',
    ];
}
