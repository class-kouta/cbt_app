<?php

namespace App\Infrastructure\Database\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CopingTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * タグに紐づくコーピング一覧
     */
    public function copings(): BelongsToMany
    {
        return $this->belongsToMany(Coping::class, 'coping_coping_tag');
    }
}
