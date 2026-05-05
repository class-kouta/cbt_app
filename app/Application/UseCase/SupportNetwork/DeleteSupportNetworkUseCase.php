<?php

namespace App\Application\UseCase\SupportNetwork;

use App\Domain\Repository\SupportNetworkRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class DeleteSupportNetworkUseCase
{
    public function __construct(private readonly SupportNetworkRepositoryInterface $supportNetworkRepository)
    {
    }

    public function handle(int $id): void
    {
        $memberId = (int) Auth::id();
        $this->supportNetworkRepository->deleteForMember($id, $memberId);
    }
}
