<?php

namespace App\Http\Controllers;

use App\Infrastructure\Database\Models\Difficulty;
use Illuminate\Http\JsonResponse;

class DifficultyController extends Controller
{
    /**
     * 難易度一覧を取得
     */
    public function index(): JsonResponse
    {
        $difficulties = Difficulty::orderBy('points')->get();

        return response()->json($difficulties);
    }
}
