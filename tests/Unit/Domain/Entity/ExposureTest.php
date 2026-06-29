<?php

namespace Tests\Unit\Domain\Entity;

use App\Domain\Entity\Exposure;
use App\Domain\Entity\ExposureSession;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ExposureTest extends TestCase
{
    public function test_can_add_new_session_when_no_sessions_exist(): void
    {
        $exposure = Exposure::createNew('テスト');

        $this->assertTrue($exposure->canAddNewSession());
    }

    public function test_can_add_new_session_when_latest_reflection_is_completed(): void
    {
        $session = ExposureSession::reconstitute(
            id: 1,
            exposureId: 1,
            hierarchyItemId: 1,
            sessionNumber: 1,
            sudsAfter: 40,
            reflection: '振り返り済み',
            createdAt: new DateTimeImmutable('now'),
            updatedAt: new DateTimeImmutable('now')
        );

        $exposure = Exposure::reconstitute(
            id: 1,
            avoidanceTarget: 'テスト',
            notes: null,
            hierarchyItems: [],
            sessions: [$session],
            createdAt: new DateTimeImmutable('now'),
            updatedAt: new DateTimeImmutable('now')
        );

        $this->assertTrue($exposure->canAddNewSession());
    }

    public function test_cannot_add_new_session_when_latest_reflection_is_incomplete(): void
    {
        $session = ExposureSession::reconstitute(
            id: 1,
            exposureId: 1,
            hierarchyItemId: 1,
            sessionNumber: 1,
            sudsAfter: 40,
            reflection: null,
            createdAt: new DateTimeImmutable('now'),
            updatedAt: new DateTimeImmutable('now')
        );

        $exposure = Exposure::reconstitute(
            id: 1,
            avoidanceTarget: 'テスト',
            notes: null,
            hierarchyItems: [],
            sessions: [$session],
            createdAt: new DateTimeImmutable('now'),
            updatedAt: new DateTimeImmutable('now')
        );

        $this->assertFalse($exposure->canAddNewSession());
    }
}
