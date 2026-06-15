<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\SelfCompassionJournal as SelfCompassionJournalEntity;
use App\Domain\Repository\SelfCompassionJournalRepositoryInterface;
use App\Infrastructure\Database\Models\SelfCompassionJournal as SelfCompassionJournalModel;

class EloquentSelfCompassionJournalRepository implements SelfCompassionJournalRepositoryInterface
{
    private function toEntity(SelfCompassionJournalModel $model): SelfCompassionJournalEntity
    {
        return SelfCompassionJournalEntity::reconstitute(
            id: (int) $model->id,
            difficultExperience: (string) $model->difficult_experience,
            effortMade: (string) $model->effort_made,
            friendVoice: (string) $model->friend_voice,
            wordToSelf: (string) $model->word_to_self,
            createdAt: $model->created_at->toDateTimeImmutable(),
            updatedAt: $model->updated_at->toDateTimeImmutable(),
        );
    }

    public function findAllForMember(int $memberId): array
    {
        return SelfCompassionJournalModel::where('member_id', $memberId)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($model) => $this->toEntity($model))
            ->values()
            ->all();
    }

    public function saveForMember(SelfCompassionJournalEntity $journal, int $memberId): SelfCompassionJournalEntity
    {
        if ($journal->getId() !== null) {
            $model = SelfCompassionJournalModel::where('member_id', $memberId)
                ->findOrFail($journal->getId());
        } else {
            $model = new SelfCompassionJournalModel();
            $model->member_id = $memberId;
        }

        $model->difficult_experience = $journal->getDifficultExperience();
        $model->effort_made = $journal->getEffortMade();
        $model->friend_voice = $journal->getFriendVoice();
        $model->word_to_self = $journal->getWordToSelf();
        $model->save();

        return $this->toEntity($model);
    }

    public function findByIdForMember(int $id, int $memberId): ?SelfCompassionJournalEntity
    {
        $model = SelfCompassionJournalModel::where('member_id', $memberId)->find($id);

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function deleteForMember(int $id, int $memberId): void
    {
        $model = SelfCompassionJournalModel::where('member_id', $memberId)->find($id);

        if ($model !== null) {
            $model->delete();
        }
    }
}
