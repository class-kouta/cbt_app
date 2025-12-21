<?php

namespace App\Infrastructure\Database\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Coping extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'point',
        'sort_order',
    ];

    protected $casts = [
        'point' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * コーピングに紐づくタグ一覧
     */
    public function copingTags(): BelongsToMany
    {
        return $this->belongsToMany(CopingTag::class, 'coping_coping_tag');
    }
}
