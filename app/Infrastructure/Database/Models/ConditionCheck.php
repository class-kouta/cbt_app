<?php

namespace App\Infrastructure\Database\Models;

use App\Infrastructure\Database\Models\Concerns\BelongsToAuthenticatedMember;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConditionCheck extends Model
{
    use BelongsToAuthenticatedMember;
    use HasFactory;

    protected $fillable = [
        'member_id',
        'mood',
        'fatigue',
        'anxiety',
        'sleepiness',
        'physical_condition',
        'memo',
    ];

    protected $casts = [
        'mood' => 'integer',
        'fatigue' => 'integer',
        'anxiety' => 'integer',
        'sleepiness' => 'integer',
        'physical_condition' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
