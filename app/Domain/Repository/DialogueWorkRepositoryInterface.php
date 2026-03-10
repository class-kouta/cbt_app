<?php

namespace App\Domain\Repository;

use App\Domain\Entity\DialogueWork;

interface DialogueWorkRepositoryInterface
{
    public function save(DialogueWork $dialogueWork): DialogueWork;

    public function findById(int $id): ?DialogueWork;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findAllOrderByCreatedAtDesc(): array;

    public function delete(int $id): void;
}
