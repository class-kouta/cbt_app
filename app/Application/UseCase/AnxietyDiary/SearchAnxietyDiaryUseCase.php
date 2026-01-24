<?php

namespace App\Application\UseCase\AnxietyDiary;

use App\Domain\Repository\AnxietyDiaryRepositoryInterface;

class SearchAnxietyDiaryUseCase
{
    /**
     * キーワード検索対象カラム
     */
    private const SEARCHABLE_COLUMNS = [
        'situation',
        'anxiety_thought',
        'actual_outcome',
    ];

    public function __construct(
        private readonly AnxietyDiaryRepositoryInterface $repository
    ) {
    }

    /**
     * 検索条件に基づいて不安日記を検索
     *
     * @param string|null $keyword 検索キーワード
     * @return array<int, array<string, mixed>> 検索結果
     */
    public function handle(?string $keyword = null): array
    {
        return $this->repository->search($keyword, self::SEARCHABLE_COLUMNS);
    }
}
