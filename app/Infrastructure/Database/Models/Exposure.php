<?php

namespace App\Infrastructure\Database\Models;

use App\Infrastructure\Database\Models\Concerns\BelongsToAuthenticatedMember;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exposure extends Model
{
    use BelongsToAuthenticatedMember;
    use HasFactory;

    protected $fillable = [
        'member_id',
        'avoidance_target',
        'exposure_type',
        'self_talk',
        'overall_reflection',
        'next_goal',
    ];

    public function hierarchyItems(): HasMany
    {
        return $this->hasMany(ExposureHierarchyItem::class)->orderBy('sort_order');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ExposureSession::class)->orderBy('session_number');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'exposure_tag');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function latestSession(): ?ExposureSession
    {
        return $this->sessions()->orderByDesc('session_number')->first();
    }

    public function canAddNewSession(): bool
    {
        $latest = $this->latestSession();

        if ($latest === null) {
            return true;
        }

        return $latest->isReflectionCompleted();
    }
}
