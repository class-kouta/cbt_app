<?php

namespace App\Infrastructure\Database\Models;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class ProblemSolving extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'problem_situation',
        'improved_image',
    ];

    /**
     * 解決策のリレーション
     */
    public function solutions(): HasMany
    {
        return $this->hasMany(ProblemSolvingSolution::class)->orderBy('sort_order');
    }

    /**
     * 実行計画・振り返りのリレーション
     */
    public function plans(): HasMany
    {
        return $this->hasMany(ProblemSolvingPlan::class)->orderBy('plan_number');
    }

    /**
     * 問題解決法に紐づくタグ一覧
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'problem_solving_tag');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * 最新の計画を取得
     */
    public function latestPlan(): ?ProblemSolvingPlan
    {
        return $this->plans()->orderByDesc('plan_number')->first();
    }

    /**
     * 新しい計画を追加できるかどうか
     * 最新の計画の振り返りが完了している場合のみ追加可能
     */
    public function canAddNewPlan(): bool
    {
        $latest = $this->latestPlan();
        
        // 計画がない場合は追加可能
        if ($latest === null) {
            return true;
        }
        
        // 最新の計画の振り返りが完了している場合は追加可能
        return $latest->isReflectionCompleted();
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? $this->getRouteKeyName(), $value)
            ->where('member_id', Auth::id())
            ->first();
    }
}
