<?php

namespace App\Application\UseCase\SupportNetwork;

use App\Application\DTO\SupportNetworkData;
use App\Domain\Entity\SupportNetwork as SupportNetworkEntity;
use App\Domain\Repository\SupportNetworkRepositoryInterface;
use DomainException;
use Illuminate\Support\Facades\Auth;

class UpdateSupportNetworkUseCase
{
    public function __construct(private readonly SupportNetworkRepositoryInterface $supportNetworkRepository)
    {
    }

    public function handle(int $id, SupportNetworkData $data): SupportNetworkEntity
    {
        $memberId = (int) Auth::id();
        $supportNetwork = $this->supportNetworkRepository->findByIdForMember($id, $memberId);

        if ($supportNetwork === null) {
            throw new DomainException('Support network not found.');
        }

        $updatedSupportNetwork = $supportNetwork->updateName($data->name, $data->point);
        return $this->supportNetworkRepository->saveForMember($updatedSupportNetwork, $memberId);
    }
}
