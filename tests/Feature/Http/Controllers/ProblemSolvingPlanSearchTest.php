<?php

namespace Tests\Feature\Http\Controllers;

use App\Infrastructure\Database\Models\ProblemSolving;
use App\Infrastructure\Database\Models\ProblemSolvingPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProblemSolvingPlanSearchTest extends TestCase
{
    use RefreshDatabase;

    private function createPlanWithData(string $problemSituation, string $actionPlan, ?string $reflection = null, ?int $improvementLevel = null): ProblemSolvingPlan
    {
        $ps = ProblemSolving::create([
            'problem_situation' => $problemSituation,
            'improved_image' => null,
        ]);

        return ProblemSolvingPlan::create([
            'problem_solving_id' => $ps->id,
            'plan_number' => 1,
            'action_plan' => $actionPlan,
            'reflection' => $reflection,
            'improvement_level' => $improvementLevel,
        ]);
    }

    public function test_plans_returns_all_plans_without_filters(): void
    {
        $this->createPlanWithData('仕事のストレス', '朝散歩する', '良い感じだった', 7);
        $this->createPlanWithData('人間関係の悩み', '友人に相談する', null, null);

        $response = $this->getJson('/api/problem-solvings/plans');

        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }

    public function test_plans_keyword_search_on_action_plan(): void
    {
        $this->createPlanWithData('仕事のストレス', '朝散歩する', null, 5);
        $this->createPlanWithData('人間関係', '友人に相談する', null, 3);

        $response = $this->getJson('/api/problem-solvings/plans?keyword=散歩');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['action_plan' => '朝散歩する']);
    }

    public function test_plans_keyword_search_on_reflection(): void
    {
        $this->createPlanWithData('仕事', '計画A', '効果があった', 8);
        $this->createPlanWithData('学校', '計画B', 'うまくいかなかった', 3);

        $response = $this->getJson('/api/problem-solvings/plans?keyword=効果');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['reflection' => '効果があった']);
    }

    public function test_plans_keyword_search_on_problem_situation(): void
    {
        $this->createPlanWithData('仕事のプレッシャー', '計画A', null, 5);
        $this->createPlanWithData('人間関係の問題', '計画B', null, 3);

        $response = $this->getJson('/api/problem-solvings/plans?keyword=プレッシャー');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['action_plan' => '計画A']);
    }

    public function test_plans_improvement_level_range_filter(): void
    {
        $this->createPlanWithData('問題1', '計画A', '振り返り', 3);
        $this->createPlanWithData('問題2', '計画B', '振り返り', 7);
        $this->createPlanWithData('問題3', '計画C', '振り返り', 9);

        $response = $this->getJson('/api/problem-solvings/plans?improvement_level_min=5&improvement_level_max=8');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['action_plan' => '計画B']);
    }

    public function test_plans_improvement_level_includes_null_at_default_range(): void
    {
        $this->createPlanWithData('問題1', '計画A', null, null);
        $this->createPlanWithData('問題2', '計画B', '振り返り', 5);

        $response = $this->getJson('/api/problem-solvings/plans');

        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }

    public function test_plans_combined_keyword_and_improvement_level_filter(): void
    {
        $this->createPlanWithData('仕事', '朝散歩する', '良かった', 8);
        $this->createPlanWithData('仕事', '朝散歩する', '微妙だった', 2);
        $this->createPlanWithData('学校', '読書する', '良かった', 8);

        $response = $this->getJson('/api/problem-solvings/plans?keyword=散歩&improvement_level_min=5&improvement_level_max=10');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['improvement_level' => 8, 'action_plan' => '朝散歩する']);
    }

    public function test_plans_excludes_empty_action_plan(): void
    {
        $ps = ProblemSolving::create([
            'problem_situation' => '問題',
            'improved_image' => null,
        ]);

        ProblemSolvingPlan::create([
            'problem_solving_id' => $ps->id,
            'plan_number' => 1,
            'action_plan' => null,
            'reflection' => null,
            'improvement_level' => null,
        ]);

        $response = $this->getJson('/api/problem-solvings/plans');

        $response->assertStatus(200);
        $response->assertJsonCount(0);
    }

    public function test_plans_validation_improvement_level_min_must_be_at_least_1(): void
    {
        $response = $this->getJson('/api/problem-solvings/plans?improvement_level_min=0');

        $response->assertStatus(422);
    }

    public function test_plans_validation_improvement_level_max_must_be_at_most_10(): void
    {
        $response = $this->getJson('/api/problem-solvings/plans?improvement_level_max=11');

        $response->assertStatus(422);
    }

    public function test_plans_validation_max_must_be_gte_min(): void
    {
        $response = $this->getJson('/api/problem-solvings/plans?improvement_level_min=8&improvement_level_max=3');

        $response->assertStatus(422);
    }

    public function test_plans_keyword_max_length_validation(): void
    {
        $longKeyword = str_repeat('あ', 256);

        $response = $this->getJson('/api/problem-solvings/plans?keyword=' . urlencode($longKeyword));

        $response->assertStatus(422);
    }
}
