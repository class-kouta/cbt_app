<?php

namespace App\Application\UseCase\SimpleNotepad;

use App\Application\DTO\SearchCriteriaData;
use App\Domain\Repository\SimpleNotepadRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class SearchSimpleNotepadUseCase
{
    /**
     * キーワード検索対象カラム
     */
    private const SEARCHABLE_COLUMNS = [
        'content',
    ];

    public function __construct(
        private readonly SimpleNotepadRepositoryInterface $repository
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function handle(SearchCriteriaData $criteria): array
    {
        return $this->repository->searchForMember($criteria, self::SEARCHABLE_COLUMNS, (int) Auth::id());
    }
}
