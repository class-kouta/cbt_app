<?php

namespace App\Application\UseCase\Auth;

use App\Domain\Repository\MemberRepositoryInterface;

class VerifyEmailUseCase
{
    public function __construct(
        private readonly MemberRepositoryInterface $memberRepository,
    ) {
    }

    public function handle(int $id, string $hash): void
    {
        $member = $this->memberRepository->findById($id);

        if (! $member) {
            abort(404);
        }

        if (! hash_equals(sha1($member->getEmailForVerification()), $hash)) {
            abort(403, '無効な認証リンクです。');
        }

        if (! $member->hasVerifiedEmail()) {
            $member->markEmailAsVerified();
        }
    }
}
