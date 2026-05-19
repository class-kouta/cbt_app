<?php

namespace App\Infrastructure\Database\Models;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class StressorAndResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'stressor',
        'cognition',
        'mood',
        'body_reaction',
        'behavior',
    ];

    /**
     * ストレッサーに紐づくタグ一覧
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'stressor_and_response_tag');
    }

    /**
     * このストレッサーから転記されたコラム一覧
     */
    public function columns(): HasMany
    {
        return $this->hasMany(Column::class, 'stressor_and_response_id');
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
