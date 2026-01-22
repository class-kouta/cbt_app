<?php

namespace App\Infrastructure\Database\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StressorAndResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'stressor',
        'cognition',
        'mood',
        'body_reaction',
        'behavior',
        'stimulated_schemas',
    ];

    protected $casts = [
        'stimulated_schemas' => 'array',
    ];

    /**
     * ストレッサーに紐づくタグ一覧
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'stressor_and_response_tag');
    }
}
