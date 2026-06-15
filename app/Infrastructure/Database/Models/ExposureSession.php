<?php

namespace App\Infrastructure\Database\Models;

use App\Infrastructure\Database\Models\Concerns\BelongsToExposureOfAuthenticatedMember;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExposureSession extends Model
{
    use BelongsToExposureOfAuthenticatedMember;
    use HasFactory;

    protected $fillable = [
        'exposure_id',
        'hierarchy_item_id',
        'session_number',
        'suds_after',
        'reflection',
    ];

    public function exposure(): BelongsTo
    {
        return $this->belongsTo(Exposure::class);
    }

    public function hierarchyItem(): BelongsTo
    {
        return $this->belongsTo(ExposureHierarchyItem::class, 'hierarchy_item_id');
    }
}
