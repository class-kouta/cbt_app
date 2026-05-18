<?php

namespace App\Application\UseCase\HappySchemaActionPlan;

use App\Application\DTO\HappySchemaActionPlanData;
use App\Domain\Entity\HappySchemaActionPlan as HappySchemaActionPlanEntity;
use App\Domain\Repository\HappySchemaActionPlanRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class UpdateHappySchemaActionPlanUseCase
{
    public function __construct(private readonly HappySchemaActionPlanRepositoryInterface $repository) {}

    public function handle(int $id, HappySchemaActionPlanData $data): HappySchemaActionPlanEntity
    {
        $memberId = (int) Auth::id();
        $existing = $this->repository->findByIdForMember($id, $memberId);

        if ($existing === null) {
            throw new \RuntimeException('ハッピースキーマと行動計画が見つかりません');
        }

        $plan = HappySchemaActionPlanEntity::reconstitute(
            $id,
            $data->happySchema,
            $data->actionPlan,
            $existing->getCreatedAt(),
            new \DateTimeImmutable('now')
        );

        return $this->repository->saveForMember($plan, $memberId);
    }
}
