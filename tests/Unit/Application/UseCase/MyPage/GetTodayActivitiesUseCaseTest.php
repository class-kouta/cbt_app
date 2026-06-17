<?php

namespace Tests\Unit\Application\UseCase\MyPage;

use App\Application\UseCase\MyPage\GetTodayActivitiesUseCase;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class GetTodayActivitiesUseCaseTest extends TestCase
{
    public function test_format_japanese_count_uses_native_counter_for_small_numbers(): void
    {
        $useCase = new GetTodayActivitiesUseCase();
        $reflection = new \ReflectionClass($useCase);
        $method = $reflection->getMethod('formatJapaneseCount');
        $method->setAccessible(true);

        $this->assertSame('一つ', $method->invoke($useCase, 1));
        $this->assertSame('二つ', $method->invoke($useCase, 2));
        $this->assertSame('十', $method->invoke($useCase, 10));
        $this->assertSame('11件', $method->invoke($useCase, 11));
    }

    public function test_build_message_uses_japanese_counter(): void
    {
        $useCase = new GetTodayActivitiesUseCase();
        $reflection = new \ReflectionClass($useCase);
        $method = $reflection->getMethod('buildMessage');
        $method->setAccessible(true);

        $this->assertSame(
            'コラム法を二つ作成しました',
            $method->invoke($useCase, 'コラム法', 2),
        );
    }
}
