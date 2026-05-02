<?php

namespace App\Application\UseCase\Auth;

use Illuminate\Support\Facades\Auth;

class LogoutUseCase
{
    public function handle(): void
    {
        Auth::guard('web')->logout();
    }
}
