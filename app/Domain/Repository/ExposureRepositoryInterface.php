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
     * @return array<string, mixed>
     */
    public function searchForMember(SearchCriteriaData $criteria, array $searchableColumns, int $memberId): array;

    /**
     * @param array<int, string> $searchableColumns
     * @return array<int, array<string, mixed>>
     */
    public function searchAllForMember(SearchCriteriaData $criteria, array $searchableColumns, int $memberId): array;

    public function saveHierarchyItemForMember(int $exposureId, ExposureHierarchyItem $item, int $memberId): ExposureHierarchyItem;

    public function updateHierarchyItemForMember(ExposureHierarchyItem $item, int $memberId): ExposureHierarchyItem;

    public function deleteHierarchyItemForMember(int $itemId, int $memberId): void;

    /**
     * @param array<int, string> $searchableColumns
     * @return array<string, mixed>
     */
    public function searchSessionsForMember(SessionSearchCriteriaData $criteria, array $searchableColumns, int $memberId): array;

    public function saveSessionForMember(int $exposureId, ExposureSession $session, int $memberId): ExposureSession;

    public function findSessionByIdForMember(int $sessionId, int $memberId): ?ExposureSession;

    public function updateSessionForMember(ExposureSession $session, int $memberId): ExposureSession;

    public function deleteSessionForMember(int $sessionId, int $memberId): void;
}
