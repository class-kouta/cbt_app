<?php

namespace Tests\Unit\Application\UseCase\ConditionCheck;

use App\Application\UseCase\ConditionCheck\PresentConditionCheckUseCase;
use App\Domain\Entity\ConditionCheck;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class PresentConditionCheckUseCaseTest extends TestCase
{
    public function test_handle_includes_score_and_score_class(): void
    {
        $entity = ConditionCheck::reconstitute(
            id: 1,
            mood: 1,
            fatigue: 2,
            anxiety: 1,
            sleepiness: 2,
            physicalCondition: 1,
            memo: 'テスト',
            createdAt: new DateTimeImmutable('2026-06-08T10:00:00+09:00'),
            updatedAt: new DateTimeImmutable('2026-06-08T10:00:00+09:00'),
        );

        $result = (new PresentConditionCheckUseCase())->handle($entity);

        $this->assertSame(7, $result['score']);
        $this->assertSame(25, $result['max_score']);
        $this->assertSame('text-blue-700', $result['score_class']);
        $this->assertSame('テスト', $result['memo']);
    }
}
