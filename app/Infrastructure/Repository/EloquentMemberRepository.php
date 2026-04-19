<?php

namespace App\Infrastructure\Repository;

use App\Domain\Repository\MemberRepositoryInterface;
use App\Models\Member;

class EloquentMemberRepository implements MemberRepositoryInterface
{
    public function findById(int $id): ?Member
    {
        return Member::find($id);
    }

    public function findByEmail(string $email): ?Member
    {
        return Member::where('email', $email)->first();
    }

    public function create(array $attributes): Member
    {
        return Member::create($attributes);
    }
}
