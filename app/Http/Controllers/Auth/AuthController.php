<?php

namespace App\Http\Controllers\Auth;

use App\Application\DTO\Auth\LoginData;
use App\Application\DTO\Auth\RegisterData;
use App\Application\Exception\EmailNotVerifiedException;
use App\Application\UseCase\Auth\LoginUseCase;
use App\Application\UseCase\Auth\LogoutUseCase;
use App\Application\UseCase\Auth\RegisterUseCase;
use App\Application\UseCase\Auth\ResendVerificationUseCase;
use App\Application\UseCase\Auth\VerifyEmailUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\MemberResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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
        $this->regenerateSession($request);

        return response()->json([
            'message' => '確認メールを送信しました。メールに記載されたURLをクリックして、会員登録を完了してください。',
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

        try {
            $member = $useCase->handle($data);
        } catch (EmailNotVerifiedException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'email_not_verified' => true,
            ], 403);
        }

        Auth::guard('web')->login($member);
        $this->regenerateSession($request);

        return (new MemberResource($member))
            ->response();
    }

    public function logout(
        Request $request,
        LogoutUseCase $useCase,
    ): JsonResponse {
        $useCase->handle();

        $this->invalidateSession($request);

        return response()->json([
            'message' => 'ログアウトしました',
        ]);
    }

    private function regenerateSession(Request $request): void
    {
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }
    }

    private function invalidateSession(Request $request): void
    {
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
    }

    public function me(Request $request): JsonResponse
    {
        return (new MemberResource($request->user()))
            ->response();
    }

    public function verifyEmail(
        Request $request,
        int $id,
        string $hash,
        VerifyEmailUseCase $useCase,
    ): RedirectResponse {
        $useCase->handle($id, $hash);

        return redirect('/login?verified=1');
    }

    public function resendVerification(
        Request $request,
        ResendVerificationUseCase $useCase,
    ): JsonResponse {
        $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $useCase->handle($request->input('email'));

        return response()->json([
            'message' => '確認メールを再送しました。メールボックスをご確認ください。',
        ]);
    }
}
