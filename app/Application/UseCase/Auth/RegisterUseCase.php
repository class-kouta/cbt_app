<?php

namespace App\Application\UseCase\Auth;

use App\Application\DTO\Auth\RegisterData;
use App\Models\Member;

class RegisterUseCase
{
    public function handle(RegisterData $data): array
    {
        $member = Member::create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => $data->password,
        ]);

        $token = $member->createToken('member-token')->plainTextToken;

        return [
            'member' => $member,
            'token' => $token,
        ];
    }
}
