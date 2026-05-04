<?php

namespace App\Application\UseCase\WritingDisclosure;

use App\Domain\Repository\WritingDisclosureRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class DeleteWritingDisclosureUseCase
{
    public function __construct(private readonly WritingDisclosureRepositoryInterface $writingDisclosureRepository)
    {
    }

    public function handle(int $id): void
    {
        $this->writingDisclosureRepository->deleteForMember($id, (int) Auth::id());
    }
}
