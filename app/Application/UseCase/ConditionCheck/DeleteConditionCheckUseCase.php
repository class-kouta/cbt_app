<?php

namespace App\Application\UseCase\ConditionCheck;

use App\Domain\Repository\ConditionCheckRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class DeleteConditionCheckUseCase
{
    public function __construct(private readonly ConditionCheckRepositoryInterface $conditionCheckRepository)
    {
    }

    public function handle(int $id): void
    {
        $this->conditionCheckRepository->deleteForMember($id, (int) Auth::id());
    }
}
