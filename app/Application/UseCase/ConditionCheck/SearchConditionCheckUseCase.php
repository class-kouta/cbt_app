<?php

namespace App\Application\UseCase\ConditionCheck;

use App\Domain\Repository\ConditionCheckRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class SearchConditionCheckUseCase
{
    public function __construct(private readonly ConditionCheckRepositoryInterface $conditionCheckRepository)
    {
    }

    public function handle(): array
    {
        return $this->conditionCheckRepository->findAllForMember((int) Auth::id());
    }
}
