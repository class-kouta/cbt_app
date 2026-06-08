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
}
