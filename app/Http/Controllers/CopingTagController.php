<?php

namespace App\Http\Controllers;

use App\Infrastructure\Database\Models\CopingTag;
use Illuminate\Http\JsonResponse;

class CopingTagController extends Controller
{
    /**
     * コーピングタグ一覧を取得
     */
    public function index(): JsonResponse
    {
        $tags = CopingTag::orderBy('name')
            ->get()
            ->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'created_at' => $tag->created_at->format(DATE_ATOM),
                    'updated_at' => $tag->updated_at->format(DATE_ATOM),
                ];
            });

        return response()->json($tags);
    }
}
