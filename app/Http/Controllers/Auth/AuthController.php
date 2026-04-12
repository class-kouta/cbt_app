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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        $result = $useCase->handle($data);

        return response()->json([
            'member' => [
                'id' => $result['member']->id,
                'name' => $result['member']->name,
                'email' => $result['member']->email,
                'created_at' => $result['member']->created_at->format(DATE_ATOM),
            ],
            'token' => $result['token'],
        ], 201);
    }

    public function login(
        LoginRequest $request,
        LoginUseCase $useCase,
    ): JsonResponse {
        $data = new LoginData(
            email: $request->validated('email'),
            password: $request->validated('password'),
        );

        $result = $useCase->handle($data);

        return response()->json([
            'member' => [
                'id' => $result['member']->id,
                'name' => $result['member']->name,
                'email' => $result['member']->email,
                'created_at' => $result['member']->created_at->format(DATE_ATOM),
            ],
            'token' => $result['token'],
        ]);
    }

    public function logout(
        Request $request,
        LogoutUseCase $useCase,
    ): JsonResponse {
        $useCase->handle($request->user());

        return response()->json([
            'message' => 'ログアウトしました',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $member = $request->user();

        return response()->json([
            'member' => [
                'id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
                'created_at' => $member->created_at->format(DATE_ATOM),
            ],
        ]);
    }
}
