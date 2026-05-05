<?php

namespace App\Application\UseCase\Chronology;

use App\Application\Service\CsvExportService;
use App\Domain\Repository\ChronologyRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportChronologyCsvUseCase
{
    private const CSV_HEADERS = [
        'ID',
        '作成日時',
        'いつ',
        '環境・出来事',
        '体験・感じたこと・思ったこと',
        'タグ',
    ];

    public function __construct(
        private readonly CsvExportService $csvExportService,
        private readonly ChronologyRepositoryInterface $chronologyRepository,
    ) {}

    public function handle(): StreamedResponse
    {
        $items = $this->chronologyRepository->findAllForMember(Auth::id());

        $sentimentLabels = [
            'positive' => 'ポジティブ',
            'negative' => 'ネガティブ',
        ];

        $rows = array_map(function ($item) use ($sentimentLabels) {
            return [
                $item->getId(),
                $this->csvExportService->formatDatetime($item->getCreatedAt()->format(DATE_ATOM)),
                $item->getWhenPeriod() ?? '',
                $item->getEnvironmentEvent() ?? '',
                $item->getExperienceFeeling() ?? '',
                $sentimentLabels[$item->getSentimentType() ?? ''] ?? '',
            ];
        }, $items);

        $filename = 'chronologies_'.$this->csvExportService->getDateSuffix().'.csv';

        return $this->csvExportService->export(self::CSV_HEADERS, $rows, $filename);
    }
}
