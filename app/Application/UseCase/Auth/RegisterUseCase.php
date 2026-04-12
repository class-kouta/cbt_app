<?php

namespace App\Application\UseCase\Auth;

use App\Application\DTO\Auth\RegisterData;
use App\Domain\Repository\MemberRepositoryInterface;
use App\Models\Member;
use Illuminate\Support\Facades\DB;

class RegisterUseCase
{
    public function __construct(
        private readonly MemberRepositoryInterface $memberRepository,
    ) {
    }

    public function handle(RegisterData $data): Member
    {
        return DB::transaction(function () use ($data) {
            return $this->memberRepository->create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => $data->password,
            ]);
        });
    }
}
