<?php

namespace Tests\Unit\Enums;

use App\Enums\ConditionCheckRating;
use PHPUnit\Framework\TestCase;

class ConditionCheckRatingTest extends TestCase
{
    public function test_max_score_is_twenty_five(): void
    {
        $this->assertSame(25, ConditionCheckRating::maxScore());
    }

    public function test_calculate_total_score_sums_all_ratings(): void
    {
        $this->assertSame(
            7,
            ConditionCheckRating::calculateTotalScore(1, 2, 1, 2, 1),
        );
    }

    public function test_score_status_for_returns_abstract_status(): void
    {
        $this->assertSame('excellent', ConditionCheckRating::scoreStatusFor(7));
        $this->assertSame('good', ConditionCheckRating::scoreStatusFor(12));
        $this->assertSame('warning', ConditionCheckRating::scoreStatusFor(17));
        $this->assertSame('danger', ConditionCheckRating::scoreStatusFor(22));
    }

    public function test_score_status_for_throws_exception_for_invalid_score(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        ConditionCheckRating::scoreStatusFor(4);
    }
}
