<?php

namespace App\Application\UseCase\Auth;

use App\Models\Member;

class LogoutUseCase
{
    public function handle(Member $member): void
    {
        $member->currentAccessToken()->delete();
    }
}
