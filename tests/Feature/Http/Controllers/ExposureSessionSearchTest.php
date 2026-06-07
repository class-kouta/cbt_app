<?php

namespace Tests\Feature\Http\Controllers;

use App\Infrastructure\Database\Models\Exposure;
use App\Infrastructure\Database\Models\ExposureSession;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExposureSessionSearchTest extends TestCase
{
    use RefreshDatabase;

    private Member $member;

    protected function setUp(): void
    {
        parent::setUp();
        $this->member = Member::factory()->create();
    }

    private function asMember(): static
    {
        return $this->actingAs($this->member, 'sanctum');
    }

    private function createSessionWithData(string $avoidanceTarget, string $actionPlan, ?string $reflection = null): ExposureSession
    {
        $exposure = Exposure::create([
            'member_id' => $this->member->id,
            'avoidance_target' => $avoidanceTarget,
        ]);

        return ExposureSession::create([
            'exposure_id' => $exposure->id,
            'session_number' => 1,
            'action_plan' => $actionPlan,
            'reflection' => $reflection,
        ]);
    }

    public function test_sessions_returns_plans_with_action_plan(): void
    {
        $this->createSessionWithData('電車が怖い', '1駅だけ乗る', 'なんとか乗れた');
        $this->createSessionWithData('人前で話すのが怖い', '短い挨拶をする', null);

        $response = $this->asMember()->getJson('/api/exposures/sessions');

        $response->assertStatus(200);
        $response->assertJsonPath('total', 2);
    }

    public function test_sessions_filter_pending(): void
    {
        $this->createSessionWithData('電車が怖い', '1駅だけ乗る', null);
        $this->createSessionWithData('人前で話す', '挨拶する', 'できた');

        $response = $this->asMember()->getJson('/api/exposures/sessions?filter=pending');

        $response->assertStatus(200);
        $response->assertJsonPath('total', 1);
    }

    public function test_exposure_crud(): void
    {
        $create = $this->asMember()->postJson('/api/exposures', [
            'avoidance_target' => 'エレベーターに乗れない',
            'exposure_type' => 'in_vivo',
        ]);

        $create->assertStatus(201);
        $id = $create->json('id');

        $this->asMember()->getJson("/api/exposures/{$id}")
            ->assertStatus(200)
            ->assertJsonFragment(['avoidance_target' => 'エレベーターに乗れない']);

        $this->asMember()->putJson("/api/exposures/{$id}", [
            'avoidance_target' => 'エレベーターに1階だけ乗る',
        ])->assertStatus(200);

        $this->asMember()->deleteJson("/api/exposures/{$id}")->assertStatus(204);
    }
}
