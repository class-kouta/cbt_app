<?php

namespace App\Infrastructure\Database\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProblemSolvingSolution extends Model
{
    use HasFactory;

    protected $fillable = [
        'problem_solving_id',
        'content',
        'effectiveness',
        'feasibility',
        'sort_order',
    ];

    protected $casts = [
        'effectiveness' => 'integer',
        'feasibility' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * 問題解決へのリレーション
     */
    public function problemSolving(): BelongsTo
    {
        return $this->belongsTo(ProblemSolving::class);
    }
}
