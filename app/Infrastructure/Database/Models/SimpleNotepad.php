<?php

namespace App\Infrastructure\Database\Models;

use App\Infrastructure\Database\Models\Concerns\BelongsToAuthenticatedMember;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SimpleNotepad extends Model
{
    use BelongsToAuthenticatedMember;
    use HasFactory;

    protected $fillable = [
        'member_id',
        'title',
        'content',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            SimpleNotepadTag::class,
            'simple_notepad_tag',
            'simple_notepad_id',
            'simple_notepad_tag_id'
        );
    }

}
