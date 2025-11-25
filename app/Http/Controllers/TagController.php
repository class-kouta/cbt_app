<?php

namespace App\Http\Controllers;

use App\Infrastructure\Database\Models\Tag;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    /**
     * タグ一覧を取得
     */
    public function index(): JsonResponse
    {
        $tags = Tag::orderBy('name')->get();

        return response()->json($tags);
    }
}
