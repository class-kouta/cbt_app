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

    public function test_positive_points_for_inverts_rating_to_positive_score(): void
    {
        $this->assertSame(5, ConditionCheckRating::positivePointsFor(1));
        $this->assertSame(1, ConditionCheckRating::positivePointsFor(5));
    }

    public function test_calculate_total_score_sums_positive_points(): void
    {
        $this->assertSame(
            23,
            ConditionCheckRating::calculateTotalScore(1, 2, 1, 2, 1),
        );
    }

    public function test_score_status_for_returns_abstract_status(): void
    {
        $this->assertSame('excellent', ConditionCheckRating::scoreStatusFor(23));
        $this->assertSame('good', ConditionCheckRating::scoreStatusFor(18));
        $this->assertSame('warning', ConditionCheckRating::scoreStatusFor(13));
        $this->assertSame('danger', ConditionCheckRating::scoreStatusFor(8));
    }

    public function test_score_status_for_throws_exception_for_invalid_score(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        ConditionCheckRating::scoreStatusFor(4);
    }
}
