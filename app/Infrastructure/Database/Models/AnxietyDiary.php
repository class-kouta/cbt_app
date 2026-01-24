<?php

namespace App\Infrastructure\Database\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnxietyDiary extends Model
{
    use HasFactory;

    protected $fillable = [
        'situation',
        'anxiety_thought',
        'actual_outcome',
        'stressor_and_response_id',
    ];

    /**
     * 転記元のストレッサーとストレス反応
     */
    public function stressorAndResponse(): BelongsTo
    {
        return $this->belongsTo(StressorAndResponse::class, 'stressor_and_response_id');
    }
}
