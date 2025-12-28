<?php

namespace App\Application\UseCase\SupportNetwork;

use App\Application\DTO\SupportNetworkData;
use App\Domain\Entity\SupportNetwork as SupportNetworkEntity;
use App\Domain\Repository\SupportNetworkRepositoryInterface;

class CreateSupportNetworkUseCase
{
    public function __construct(private readonly SupportNetworkRepositoryInterface $supportNetworkRepository)
    {
    }

    public function handle(SupportNetworkData $data): SupportNetworkEntity
    {
        $supportNetwork = SupportNetworkEntity::createNew($data->name);
        return $this->supportNetworkRepository->save($supportNetwork);
    }
}
