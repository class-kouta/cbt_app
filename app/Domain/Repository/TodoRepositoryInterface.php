<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Todo;

interface TodoRepositoryInterface
{
    public function save(Todo $todo): Todo;
    public function uncomplete(int $todoId): void;
}

