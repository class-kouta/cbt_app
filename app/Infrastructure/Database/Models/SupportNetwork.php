<?php

namespace App\Infrastructure\Database\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportNetwork extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'point',
    ];

    protected $casts = [
        'point' => 'integer',
    ];
}
