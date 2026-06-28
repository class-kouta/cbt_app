<?php

namespace App\Http\Controllers;

use App\Application\UseCase\MyPage\GetTodayActivitiesUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MyPageController extends Controller
{
    public function todayActivities(
        Request $request,
        GetTodayActivitiesUseCase $useCase,
    ): JsonResponse {
        $user = $request->user();
        if ($user === null) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json(
            $useCase->handle((int) $user->id),
        );
    }
}
