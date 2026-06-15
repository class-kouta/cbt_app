<?php

namespace App\Domain\Repository;

use App\Domain\Entity\SelfCompassionJournal;

interface SelfCompassionJournalRepositoryInterface
{
    /**
     * @return list<SelfCompassionJournal>
     */
    public function findAllForMember(int $memberId): array;

    public function saveForMember(SelfCompassionJournal $journal, int $memberId): SelfCompassionJournal;
}
