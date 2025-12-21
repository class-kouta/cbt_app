<?php

namespace App\Application\UseCase\Coping;

use App\Domain\Repository\CopingRepositoryInterface;

class ReorderCopingsUseCase
{
    public function __construct(private readonly CopingRepositoryInterface $copingRepository)
    {
    }

    /**
     * コーピングの並び順を更新
     *
     * @param int[] $orderedIds 並び順通りのIDの配列
     */
    public function handle(array $orderedIds): void
    {
        $orderMap = [];
        foreach ($orderedIds as $index => $id) {
            $orderMap[(int) $id] = $index + 1;
        }

        $this->copingRepository->updateSortOrders($orderMap);
    }
}
