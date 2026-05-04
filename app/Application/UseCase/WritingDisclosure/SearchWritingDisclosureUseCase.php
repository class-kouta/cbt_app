<?php

namespace App\Application\UseCase\WritingDisclosure;

use App\Domain\Repository\WritingDisclosureRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class SearchWritingDisclosureUseCase
{
    public function __construct(private readonly WritingDisclosureRepositoryInterface $writingDisclosureRepository)
    {
    }

    public function handle(): array
    {
        return $this->writingDisclosureRepository->findAllForMember((int) Auth::id());
    }
}
