<?php

namespace App\Infrastructure\Database\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExposureHierarchyItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'exposure_id',
        'content',
        'expected_suds',
        'sort_order',
    ];

    public function exposure(): BelongsTo
    {
        return $this->belongsTo(Exposure::class);
    }
}
