<?php

namespace App\Application\UseCase\SupportNetwork;

use App\Application\DTO\SupportNetworkData;
use App\Domain\Entity\SupportNetwork as SupportNetworkEntity;
use App\Domain\Repository\SupportNetworkRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CreateSupportNetworkUseCase
{
    public function __construct(private readonly SupportNetworkRepositoryInterface $supportNetworkRepository)
    {
    }

    public function handle(SupportNetworkData $data): SupportNetworkEntity
    {
        $memberId = (int) Auth::id();
        $supportNetwork = SupportNetworkEntity::createNew($data->name);
        return $this->supportNetworkRepository->saveForMember($supportNetwork, $memberId);
    }
}
