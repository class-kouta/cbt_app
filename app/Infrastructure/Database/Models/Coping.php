<?php

namespace App\Infrastructure\Database\Models;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class Coping extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'content',
        'point',
    ];

    protected $casts = [
        'point' => 'integer',
    ];

    /**
     * コーピングに紐づくタグ一覧
     */
    public function copingTags(): BelongsToMany
    {
        return $this->belongsToMany(CopingTag::class, 'coping_coping_tag');
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
