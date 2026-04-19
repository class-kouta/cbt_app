<?php

namespace App\Application\UseCase\Auth;

use App\Domain\Repository\MemberRepositoryInterface;

class ResendVerificationUseCase
{
    public function __construct(
        private readonly MemberRepositoryInterface $memberRepository,
    ) {
    }

    public function handle(string $email): void
    {
        $member = $this->memberRepository->findByEmail($email);

        if ($member && ! $member->hasVerifiedEmail()) {
            $member->sendEmailVerificationNotification();
        }
    }
}
