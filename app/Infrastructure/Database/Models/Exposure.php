<?php

namespace App\Infrastructure\Database\Models;

use App\Infrastructure\Database\Models\Concerns\BelongsToAuthenticatedMember;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exposure extends Model
{
    use BelongsToAuthenticatedMember;
    use HasFactory;

    protected $fillable = [
        'member_id',
        'avoidance_target',
    ];

    public function hierarchyItems(): HasMany
    {
        return $this->hasMany(ExposureHierarchyItem::class)->orderBy('sort_order');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ExposureSession::class)->orderBy('session_number');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
