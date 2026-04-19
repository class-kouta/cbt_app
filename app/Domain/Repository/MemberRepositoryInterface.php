<?php

namespace App\Domain\Repository;

use App\Models\Member;

interface MemberRepositoryInterface
{
    public function findById(int $id): ?Member;

    public function findByEmail(string $email): ?Member;

    public function create(array $attributes): Member;
}
