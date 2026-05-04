<?php

namespace App\Infrastructure\Database\Models;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class Column extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'situation',
        'mood',
        'automatic_thought',
        'evidence',
        'counter_evidence',
        'adaptive_thought',
        'current_mood',
        'notes',
        'stressor_and_response_id',
    ];

    /**
     * コラムに紐づくタグ一覧
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'column_tag');
    }

    /**
     * 転記元のストレッサーとストレス反応
     */
    public function stressorAndResponse(): BelongsTo
    {
        return $this->belongsTo(StressorAndResponse::class, 'stressor_and_response_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? $this->getRouteKeyName(), $value)
            ->where('member_id', Auth::id())
            ->first();
    }
}
