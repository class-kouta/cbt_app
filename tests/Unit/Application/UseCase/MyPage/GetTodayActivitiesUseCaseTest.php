<?php

namespace Tests\Unit\Application\UseCase\MyPage;

use App\Application\UseCase\MyPage\GetTodayActivitiesUseCase;
use PHPUnit\Framework\TestCase;

class GetTodayActivitiesUseCaseTest extends TestCase
{
    public function test_build_message_uses_count_with_kan_suffix(): void
    {
        $useCase = new GetTodayActivitiesUseCase();
        $reflection = new \ReflectionClass($useCase);
        $method = $reflection->getMethod('buildMessage');
        $method->setAccessible(true);

        $this->assertSame(
            'コラム法を2件作成しました',
            $method->invoke($useCase, 'コラム法', 2),
        );
        $this->assertSame(
            'コンディションチェックを1件作成しました',
            $method->invoke($useCase, 'コンディションチェック', 1),
        );
    }
}
