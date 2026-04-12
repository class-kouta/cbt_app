<?php

namespace App\Application\UseCase\Auth;

use App\Application\DTO\Auth\LoginData;
use App\Domain\Repository\MemberRepositoryInterface;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginUseCase
{
    public function __construct(
        private readonly MemberRepositoryInterface $memberRepository,
    ) {
    }

    public function handle(LoginData $data): Member
    {
        $member = $this->memberRepository->findByEmail($data->email);

        if (! $member || ! Hash::check($data->password, $member->password)) {
            throw ValidationException::withMessages([
                'email' => ['メールアドレスまたはパスワードが正しくありません。'],
            ]);
        }

        return $member;
    }
}
