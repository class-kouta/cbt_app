<?php

namespace App\Application\Service;

use App\Domain\Entity\Exposure as ExposureEntity;
use App\Domain\Entity\ExposureHierarchyItem as ExposureHierarchyItemEntity;
use App\Domain\Entity\ExposureSession as ExposureSessionEntity;

class ExposureResponseFormatter
{
    /**
     * @return array<string, mixed>
     */
    public function exposureFromEntity(ExposureEntity $exposure): array
    {
        return [
            'id' => $exposure->getId(),
            'avoidance_target' => $exposure->getAvoidanceTarget(),
            'notes' => $exposure->getNotes(),
            'hierarchy_items' => array_map(
                fn (ExposureHierarchyItemEntity $item) => $this->hierarchyItemFromEntity($item),
                $exposure->getHierarchyItems()
            ),
            'sessions' => array_map(
                fn (ExposureSessionEntity $session) => $this->sessionFromEntity($session),
                $exposure->getSessions()
            ),
            'created_at' => $exposure->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $exposure->getUpdatedAt()->format(DATE_ATOM),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function hierarchyItemFromEntity(ExposureHierarchyItemEntity $item): array
    {
        return [
            'id' => $item->getId(),
            'exposure_id' => $item->getExposureId(),
            'content' => $item->getContent(),
            'expected_suds' => $item->getExpectedSuds(),
            'sort_order' => $item->getSortOrder(),
            'created_at' => $item->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $item->getUpdatedAt()->format(DATE_ATOM),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function sessionFromEntity(ExposureSessionEntity $session): array
    {
        return [
            'id' => $session->getId(),
            'exposure_id' => $session->getExposureId(),
            'hierarchy_item_id' => $session->getHierarchyItemId(),
            'session_number' => $session->getSessionNumber(),
            'suds_after' => $session->getSudsAfter(),
            'reflection' => $session->getReflection(),
            'created_at' => $session->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $session->getUpdatedAt()->format(DATE_ATOM),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function sessionSearchRowFromEntity(
        ExposureSessionEntity $session,
        string $avoidanceTarget,
        string $hierarchyItemContent
    ): array {
        return array_merge($this->sessionFromEntity($session), [
            'avoidance_target' => $avoidanceTarget,
            'hierarchy_item_content' => $hierarchyItemContent,
        ]);
    }

    /**
     * @param array{id: int, avoidance_target: string} $option
     * @return array{id: int, avoidance_target: string}
     */
    public function optionFromArray(array $option): array
    {
        return [
            'id' => $option['id'],
            'avoidance_target' => $option['avoidance_target'],
        ];
    }
}
