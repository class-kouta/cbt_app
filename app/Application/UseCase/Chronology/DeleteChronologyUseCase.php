<?php

namespace App\Application\UseCase\Chronology;

use App\Domain\Repository\ChronologyRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class DeleteChronologyUseCase
{
    public function __construct(private readonly ChronologyRepositoryInterface $chronologyRepository) {}

    public function handle(int $id): void
    {
        $this->chronologyRepository->deleteForMember($id, Auth::id());
    }
}
