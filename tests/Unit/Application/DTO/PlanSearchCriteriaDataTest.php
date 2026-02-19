<?php

namespace Tests\Unit\Application\DTO;

use App\Application\DTO\PlanSearchCriteriaData;
use PHPUnit\Framework\TestCase;

class PlanSearchCriteriaDataTest extends TestCase
{
    public function test_default_values(): void
    {
        $criteria = new PlanSearchCriteriaData();

        $this->assertNull($criteria->keyword);
        $this->assertSame(1, $criteria->improvementLevelMin);
        $this->assertSame(10, $criteria->improvementLevelMax);
    }

    public function test_hasKeyword_returns_true_when_keyword_is_set(): void
    {
        $criteria = new PlanSearchCriteriaData(keyword: 'test');

        $this->assertTrue($criteria->hasKeyword());
    }

    public function test_hasKeyword_returns_false_when_keyword_is_null(): void
    {
        $criteria = new PlanSearchCriteriaData();

        $this->assertFalse($criteria->hasKeyword());
    }

    public function test_hasKeyword_returns_false_when_keyword_is_empty(): void
    {
        $criteria = new PlanSearchCriteriaData(keyword: '');

        $this->assertFalse($criteria->hasKeyword());
    }

    public function test_hasImprovementLevelFilter_returns_false_at_default(): void
    {
        $criteria = new PlanSearchCriteriaData();

        $this->assertFalse($criteria->hasImprovementLevelFilter());
    }

    public function test_hasImprovementLevelFilter_returns_true_when_min_changed(): void
    {
        $criteria = new PlanSearchCriteriaData(improvementLevelMin: 3);

        $this->assertTrue($criteria->hasImprovementLevelFilter());
    }

    public function test_hasImprovementLevelFilter_returns_true_when_max_changed(): void
    {
        $criteria = new PlanSearchCriteriaData(improvementLevelMax: 7);

        $this->assertTrue($criteria->hasImprovementLevelFilter());
    }

    public function test_hasImprovementLevelFilter_returns_true_when_both_changed(): void
    {
        $criteria = new PlanSearchCriteriaData(improvementLevelMin: 3, improvementLevelMax: 7);

        $this->assertTrue($criteria->hasImprovementLevelFilter());
    }

    public function test_custom_values(): void
    {
        $criteria = new PlanSearchCriteriaData(
            keyword: '行動実験',
            improvementLevelMin: 5,
            improvementLevelMax: 8
        );

        $this->assertSame('行動実験', $criteria->keyword);
        $this->assertSame(5, $criteria->improvementLevelMin);
        $this->assertSame(8, $criteria->improvementLevelMax);
        $this->assertTrue($criteria->hasKeyword());
        $this->assertTrue($criteria->hasImprovementLevelFilter());
    }
}
