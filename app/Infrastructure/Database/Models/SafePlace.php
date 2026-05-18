<?php

namespace App\Infrastructure\Database\Models;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class SafePlace extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'safe_image',
        'safe_something',
    ];

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
