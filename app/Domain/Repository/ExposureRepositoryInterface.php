<?php

namespace App\Domain\Repository;

use App\Application\DTO\SearchCriteriaData;
use App\Application\DTO\SessionSearchCriteriaData;
use App\Domain\Entity\Exposure;
use App\Domain\Entity\ExposureHierarchyItem;
use App\Domain\Entity\ExposureSession;

interface ExposureRepositoryInterface
{
    public function saveForMember(Exposure $exposure, int $memberId): Exposure;

    public function findByIdForMember(int $id, int $memberId): ?Exposure;

    public function deleteForMember(int $id, int $memberId): void;

    /**
     * @param array<int, string> $searchableColumns
     * @return array{
     *     data: Exposure[],
     *     total: int,
     *     per_page: int,
     *     current_page: int,
     *     last_page: int,
     *     from: int|null,
     *     to: int|null
     * }
     */
    public function searchForMember(SearchCriteriaData $criteria, array $searchableColumns, int $memberId): array;

    /**
     * @param array<int, string> $searchableColumns
     * @return \Generator<int, Exposure>
     */
    public function cursorAllForMember(SearchCriteriaData $criteria, array $searchableColumns, int $memberId): \Generator;

    /**
     * @return array<int, array{id: int, avoidance_target: string}>
     */
    public function listOptionsForMember(int $memberId): array;

    public function saveHierarchyItemForMember(int $exposureId, ExposureHierarchyItem $item, int $memberId): ExposureHierarchyItem;

    public function findHierarchyItemByIdForMember(int $itemId, int $memberId): ?ExposureHierarchyItem;

    public function updateHierarchyItemForMember(ExposureHierarchyItem $item, int $memberId): ExposureHierarchyItem;

    public function deleteHierarchyItemForMember(int $itemId, int $memberId): void;

    /**
     * @param array<int, string> $searchableColumns
     * @return array{
     *     data: array<int, array{
     *         session: ExposureSession,
     *         avoidance_target: string,
     *         hierarchy_item_content: string
     *     }>,
     *     total: int,
     *     current_page: int,
     *     last_page: int,
     *     per_page: int,
     *     from: int|null,
     *     to: int|null
     * }
     */
    public function searchSessionsForMember(SessionSearchCriteriaData $criteria, array $searchableColumns, int $memberId): array;

    public function saveSessionForMember(int $exposureId, ExposureSession $session, int $memberId): ExposureSession;

    public function findSessionByIdForMember(int $sessionId, int $memberId): ?ExposureSession;

    public function updateSessionForMember(ExposureSession $session, int $memberId): ExposureSession;

    public function deleteSessionForMember(int $sessionId, int $memberId): void;

    /**
     * @param ExposureHierarchyItem[] $items
     * @return ExposureHierarchyItem[]
     */
    public function syncHierarchyItemsForMember(int $exposureId, array $items, int $memberId): array;

    /**
     * @param ExposureSession[] $sessions
     * @return ExposureSession[]
     */
    public function syncSessionsForMember(int $exposureId, array $sessions, int $memberId): array;

    public function hierarchyItemBelongsToExposureForMember(int $hierarchyItemId, int $exposureId, int $memberId): bool;
}
