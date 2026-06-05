<?php

namespace App\Infrastructure\Database\Models;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class SimpleNotepadTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'name',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function simpleNotepads(): BelongsToMany
    {
        return $this->belongsToMany(
            SimpleNotepad::class,
            'simple_notepad_tag',
            'simple_notepad_tag_id',
            'simple_notepad_id'
        );
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? $this->getRouteKeyName(), $value)
            ->where('member_id', Auth::id())
            ->first();
    }
}
