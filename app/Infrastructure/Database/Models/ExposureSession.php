<?php

namespace App\Infrastructure\Database\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExposureSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'exposure_id',
        'hierarchy_item_id',
        'session_number',
        'action_plan',
        'suds_before',
        'suds_peak',
        'suds_after',
        'performed_at',
        'reflection',
    ];

    protected $casts = [
        'performed_at' => 'immutable_datetime',
    ];

    public function exposure(): BelongsTo
    {
        return $this->belongsTo(Exposure::class);
    }

    public function hierarchyItem(): BelongsTo
    {
        return $this->belongsTo(ExposureHierarchyItem::class, 'hierarchy_item_id');
    }

    public function isReflectionCompleted(): bool
    {
        return $this->reflection !== null && trim($this->reflection) !== '';
    }
}
