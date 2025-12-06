<?php

namespace App\Http\Controllers;

use App\Infrastructure\Database\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * タグ一覧を取得（削除されていないタグのみ）
     * TODO作成画面などで使用
     */
    public function index(): JsonResponse
    {
        $tags = Tag::orderBy('name')->get();

        return response()->json($tags);
    }

    /**
     * タグ一覧を取得（削除されたタグも含む）
     * 管理画面のタグ管理用
     */
    public function indexWithTrashed(): JsonResponse
    {
        $tags = Tag::withTrashed()->orderBy('name')->get();

        return response()->json($tags);
    }

    /**
     * タグを新規作成
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:tags,name',
        ], [
            'name.required' => 'タグ名を入力してください',
            'name.max' => 'タグ名は50文字以内で入力してください',
            'name.unique' => 'このタグ名は既に存在します',
        ]);

        $tag = Tag::create($validated);

        return response()->json($tag, 201);
    }

    /**
     * タグを更新
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $tag = Tag::withTrashed()->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:tags,name,' . $id,
        ], [
            'name.required' => 'タグ名を入力してください',
            'name.max' => 'タグ名は50文字以内で入力してください',
            'name.unique' => 'このタグ名は既に存在します',
        ]);

        $tag->update($validated);

        return response()->json($tag);
    }

    /**
     * タグを削除（論理削除）
     */
    public function destroy(int $id): JsonResponse
    {
        $tag = Tag::findOrFail($id);
        $tag->delete();

        return response()->json(null, 204);
    }

    /**
     * 削除されたタグを復元
     */
    public function restore(int $id): JsonResponse
    {
        $tag = Tag::withTrashed()->findOrFail($id);
        $tag->restore();

        return response()->json($tag);
    }
}
