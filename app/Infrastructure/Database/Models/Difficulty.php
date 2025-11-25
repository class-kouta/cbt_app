<?php

namespace App\Infrastructure\Database\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Difficulty extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'points',
        'color',
    ];

    protected $casts = [
        'points' => 'integer',
    ];

    /**
     * 難易度に紐づくTODO一覧
     */
    public function todos(): HasMany
    {
        return $this->hasMany(Todo::class);
    }
}
