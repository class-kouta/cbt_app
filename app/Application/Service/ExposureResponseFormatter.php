<?php

namespace App\Application\Service;

use App\Domain\Entity\Exposure as ExposureEntity;
use App\Domain\Entity\ExposureHierarchyItem as ExposureHierarchyItemEntity;
use App\Domain\Entity\ExposureSession as ExposureSessionEntity;
use App\Infrastructure\Database\Models\Exposure as ExposureModel;
use App\Infrastructure\Database\Models\ExposureSession as ExposureSessionModel;

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
    public function exposureFromModel(ExposureModel $exposure): array
    {
        $exposure->loadMissing(['hierarchyItems', 'sessions']);

        return [
            'id' => $exposure->id,
            'avoidance_target' => $exposure->avoidance_target,
            'hierarchy_items' => $exposure->hierarchyItems->map(
                fn ($item) => $this->hierarchyItemFromModel($item)
            )->toArray(),
            'sessions' => $exposure->sessions->map(
                fn ($session) => $this->sessionFromModel($session)
            )->toArray(),
            'created_at' => $exposure->created_at->format(DATE_ATOM),
            'updated_at' => $exposure->updated_at->format(DATE_ATOM),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function hierarchyItemFromEntity(ExposureHierarchyItemEntity $item): array
    {
        return [
            'id' => $item->getId(),
            'content' => $item->getContent(),
            'expected_suds' => $item->getExpectedSuds(),
            'sort_order' => $item->getSortOrder(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function hierarchyItemFromModel(object $item): array
    {
        return [
            'id' => $item->id,
            'content' => $item->content,
            'expected_suds' => $item->expected_suds,
            'sort_order' => $item->sort_order,
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
    public function sessionFromModel(ExposureSessionModel $session): array
    {
        return [
            'id' => $session->id,
            'exposure_id' => $session->exposure_id,
            'hierarchy_item_id' => $session->hierarchy_item_id,
            'session_number' => $session->session_number,
            'suds_after' => $session->suds_after,
            'reflection' => $session->reflection,
            'created_at' => $session->created_at->format(DATE_ATOM),
            'updated_at' => $session->updated_at->format(DATE_ATOM),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function sessionSearchRowFromModel(ExposureSessionModel $session): array
    {
        return array_merge($this->sessionFromModel($session), [
            'avoidance_target' => $session->exposure->avoidance_target ?? '',
            'hierarchy_item_content' => $session->hierarchyItem->content ?? '',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function sessionDetailFromModel(ExposureSessionModel $session): array
    {
        $session->loadMissing(['exposure', 'hierarchyItem']);

        return array_merge($this->sessionFromModel($session), [
            'avoidance_target' => $session->exposure->avoidance_target ?? '',
            'hierarchy_item_content' => $session->hierarchyItem->content ?? '',
        ]);
    }
}
