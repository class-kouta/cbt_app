<?php

namespace App\Infrastructure\Database\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProblemSolving extends Model
{
    use HasFactory;

    protected $fillable = [
        'problem_situation',
        'improved_image',
        'action_plan',
        'reflection',
    ];

    /**
     * 解決策のリレーション
     */
    public function solutions(): HasMany
    {
        return $this->hasMany(ProblemSolvingSolution::class)->orderBy('sort_order');
    }
}
