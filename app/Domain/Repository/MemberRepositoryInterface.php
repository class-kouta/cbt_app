<?php

namespace App\Domain\Repository;

use App\Models\Member;

interface MemberRepositoryInterface
{
    public function findByEmail(string $email): ?Member;

    public function create(array $attributes): Member;
}
