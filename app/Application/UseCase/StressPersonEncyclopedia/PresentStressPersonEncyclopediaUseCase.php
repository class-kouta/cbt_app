<?php

namespace App\Application\UseCase\StressPersonEncyclopedia;

use App\Domain\Entity\StressPersonEncyclopedia;

class PresentStressPersonEncyclopediaUseCase
{
    /**
     * @return array<string, mixed>
     */
    public function handle(StressPersonEncyclopedia $encyclopedia): array
    {
        return [
            'id' => $encyclopedia->getId(),
            'name' => $encyclopedia->getName(),
            'relationship' => $encyclopedia->getRelationship(),
            'difficult_traits' => $encyclopedia->getDifficultTraits(),
            'my_reaction' => $encyclopedia->getMyReaction(),
            'coping_strategy' => $encyclopedia->getCopingStrategy(),
            'notes' => $encyclopedia->getNotes(),
            'created_at' => $encyclopedia->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $encyclopedia->getUpdatedAt()->format(DATE_ATOM),
        ];
    }
}
