<?php

namespace App\Application\UseCase\HappySchemaActionPlan;

use App\Application\Service\CsvExportService;
use App\Domain\Entity\HappySchemaActionPlan as HappySchemaActionPlanEntity;
use App\Domain\Repository\HappySchemaActionPlanRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportHappySchemaActionPlanCsvUseCase
{
    private const CSV_HEADERS = [
        'ID',
        'ハッピースキーマ',
        'ハッピースキーマに基づく行動計画',
    ];

    public function __construct(
        private readonly CsvExportService $csvExportService,
        private readonly HappySchemaActionPlanRepositoryInterface $repository
    ) {}

    public function handle(): StreamedResponse
    {
        $plans = $this->repository->findAllForMemberOrderedByLatest((int) Auth::id());

        $rows = array_map(function (HappySchemaActionPlanEntity $plan) {
            return [
                $plan->getId(),
                $plan->getHappySchema() ?? '',
                $plan->getActionPlan() ?? '',
            ];
        }, $plans);

        $filename = 'happy_schema_action_plans_'.$this->csvExportService->getDateSuffix().'.csv';

        return $this->csvExportService->export(self::CSV_HEADERS, $rows, $filename);
    }
}
