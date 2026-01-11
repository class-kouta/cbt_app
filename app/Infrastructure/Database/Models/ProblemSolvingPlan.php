<?php

namespace App\Infrastructure\Database\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProblemSolvingPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'problem_solving_id',
        'plan_number',
        'action_plan',
        'reflection',
    ];

    protected $casts = [
        'plan_number' => 'integer',
    ];

    /**
     * 問題解決へのリレーション
     */
    public function problemSolving(): BelongsTo
    {
        return $this->belongsTo(ProblemSolving::class);
    }

    /**
     * 振り返りが完了しているかどうか
     */
    public function isReflectionCompleted(): bool
    {
        return $this->reflection !== null && trim($this->reflection) !== '';
    }
}
