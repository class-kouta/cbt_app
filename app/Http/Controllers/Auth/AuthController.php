<?php

namespace App\Http\Controllers\Auth;

use App\Application\DTO\Auth\LoginData;
use App\Application\DTO\Auth\RegisterData;
use App\Application\UseCase\Auth\LoginUseCase;
use App\Application\UseCase\Auth\LogoutUseCase;
use App\Application\UseCase\Auth\RegisterUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\MemberResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(
        RegisterRequest $request,
        RegisterUseCase $useCase,
    ): JsonResponse {
        $data = new RegisterData(
            name: $request->validated('name'),
            email: $request->validated('email'),
            password: $request->validated('password'),
        );

        $member = $useCase->handle($data);

        Auth::guard('web')->login($member);
        $request->session()->regenerate();

        return (new MemberResource($member))
            ->response()
            ->setStatusCode(201);
    }

    public function login(
        LoginRequest $request,
        LoginUseCase $useCase,
    ): JsonResponse {
        $data = new LoginData(
            email: $request->validated('email'),
            password: $request->validated('password'),
        );

        $member = $useCase->handle($data);

        Auth::guard('web')->login($member);
        $request->session()->regenerate();

        return (new MemberResource($member))
            ->response();
    }

    public function logout(
        Request $request,
        LogoutUseCase $useCase,
    ): JsonResponse {
        $useCase->handle();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'ログアウトしました',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return (new MemberResource($request->user()))
            ->response();
    }
}
