<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Coping;

interface CopingRepositoryInterface
{
    public function save(Coping $coping): Coping;

    public function findById(int $id): ?Coping;

    public function delete(int $id): void;

    /**
     * 複数コーピングの並び順を一括更新
     *
     * @param array<int, int> $orderMap IDをキー、sort_orderを値とする配列
     */
    public function updateSortOrders(array $orderMap): void;

    /**
     * 次のsort_orderを取得（新規作成時用）
     */
    public function getNextSortOrder(): int;
}
