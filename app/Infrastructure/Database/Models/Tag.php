<?php

namespace App\Infrastructure\Database\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * タグに紐づくストレッサーとストレス反応一覧
     */
    public function stressorAndResponses(): BelongsToMany
    {
        return $this->belongsToMany(StressorAndResponse::class, 'stressor_and_response_tag');
    }

    /**
     * タグに紐づくコラム一覧
     */
    public function columns(): BelongsToMany
    {
        return $this->belongsToMany(Column::class, 'column_tag');
    }

    /**
     * タグに紐づく問題解決法一覧
     */
    public function problemSolvings(): BelongsToMany
    {
        return $this->belongsToMany(ProblemSolving::class, 'problem_solving_tag');
    }

}
