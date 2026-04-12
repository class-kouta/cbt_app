<?php

namespace App\Application\UseCase\Auth;

use App\Application\DTO\Auth\LoginData;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginUseCase
{
    public function handle(LoginData $data): array
    {
        $member = Member::where('email', $data->email)->first();

        if (! $member || ! Hash::check($data->password, $member->password)) {
            throw ValidationException::withMessages([
                'email' => ['メールアドレスまたはパスワードが正しくありません。'],
            ]);
        }

        $member->tokens()->delete();
        $token = $member->createToken('member-token')->plainTextToken;

        return [
            'member' => $member,
            'token' => $token,
        ];
    }
}
