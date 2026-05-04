<?php

namespace App\Application\UseCase\Coping;

use App\Domain\Repository\CopingRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class DeleteCopingUseCase
{
    public function __construct(private readonly CopingRepositoryInterface $copingRepository)
    {
    }

    public function handle(int $id): void
    {
        $this->copingRepository->deleteForMember($id, (int) Auth::id());
    }
}
