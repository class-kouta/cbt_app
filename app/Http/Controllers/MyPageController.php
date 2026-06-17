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
        return response()->json(
            $useCase->handle((int) $request->user()->id),
        );
    }
}
