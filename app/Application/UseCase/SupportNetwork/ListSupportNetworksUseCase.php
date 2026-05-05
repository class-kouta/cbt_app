<?php

namespace App\Application\UseCase\SupportNetwork;

use App\Domain\Repository\SupportNetworkRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class ListSupportNetworksUseCase
{
    public function __construct(private readonly SupportNetworkRepositoryInterface $supportNetworkRepository)
    {
    }

    public function handle(): array
    {
        $memberId = (int) Auth::id();

        return $this->supportNetworkRepository->findAllForMember($memberId);
    }
}
