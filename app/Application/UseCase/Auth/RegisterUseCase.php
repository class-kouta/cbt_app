<?php

namespace App\Application\UseCase\Auth;

use App\Application\DTO\Auth\RegisterData;
use App\Domain\Repository\MemberRepositoryInterface;
use App\Models\Member;

class RegisterUseCase
{
    public function __construct(
        private readonly MemberRepositoryInterface $memberRepository,
    ) {
    }

    public function handle(RegisterData $data): Member
    {
        return $this->memberRepository->create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => $data->password,
        ]);
    }
}
