<?php

namespace App\Infrastructure\Database\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * このストレッサーから転記されたコラム一覧
     */
    public function columns(): HasMany
    {
        return $this->hasMany(Column::class, 'stressor_and_response_id');
    }
}
