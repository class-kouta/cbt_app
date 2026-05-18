<?php

namespace App\Infrastructure\Database\Models;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class SupportNetwork extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'name',
        'point',
    ];

    protected $casts = [
        'point' => 'integer',
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
