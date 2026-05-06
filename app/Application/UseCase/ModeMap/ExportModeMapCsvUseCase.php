<?php

namespace App\Application\UseCase\ModeMap;

use App\Application\Service\CsvExportService;
use App\Domain\Entity\ModeMap;
use App\Domain\Repository\ModeMapRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportModeMapCsvUseCase
{
    private const CSV_HEADERS = [
        'ID',
        '作成日時',
        '傷ついた子どもモード',
        '傷つける大人モード',
        'いただけない対処モード',
        'ヘルシーモード(幸せな子どもモード)',
        'ヘルシーモード(ヘルシーな大人モード)',
    ];

    public function __construct(
        private readonly CsvExportService $csvExportService,
        private readonly ModeMapRepositoryInterface $repository
    ) {}

    public function handle(): StreamedResponse
    {
        $modeMap = $this->repository->findFirstForMember((int) Auth::id());

        $rows = $modeMap === null ? [] : [$this->toRow($modeMap)];

        $filename = 'mode_maps_'.$this->csvExportService->getDateSuffix().'.csv';

        return $this->csvExportService->export(self::CSV_HEADERS, $rows, $filename);
    }

    private function toRow(ModeMap $modeMap): array
    {
        $sanitize = function ($value) {
            if (is_string($value) && strlen($value) > 0 && in_array($value[0], ['=', '+', '-', '@'], true)) {
                return "'".$value;
            }

            return $value;
        };

        return [
            $modeMap->getId(),
            $this->csvExportService->formatDatetime($modeMap->getCreatedAt()->format('Y-m-d H:i:s')),
            $sanitize($modeMap->getWoundedChildMode() ?? ''),
            $sanitize($modeMap->getHurtfulAdultMode() ?? ''),
            $sanitize($modeMap->getUnacceptableCopingMode() ?? ''),
            $sanitize($modeMap->getHealthyHappyChildMode() ?? ''),
            $sanitize($modeMap->getHealthyAdultMode() ?? ''),
        ];
    }
}
