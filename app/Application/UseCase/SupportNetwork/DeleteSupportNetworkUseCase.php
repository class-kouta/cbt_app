<?php

namespace App\Application\UseCase\SupportNetwork;

use App\Domain\Repository\SupportNetworkRepositoryInterface;

class DeleteSupportNetworkUseCase
{
    public function __construct(private readonly SupportNetworkRepositoryInterface $supportNetworkRepository)
    {
    }

    public function handle(int $id): void
    {
        $this->supportNetworkRepository->delete($id);
    }
}
