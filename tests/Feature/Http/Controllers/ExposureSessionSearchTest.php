<?php

namespace Tests\Feature\Http\Controllers;

use App\Infrastructure\Database\Models\Exposure;
use App\Infrastructure\Database\Models\ExposureHierarchyItem;
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

    public function test_sync_hierarchy_items_in_single_request(): void
    {
        $exposure = Exposure::create([
            'member_id' => $this->member->id,
            'avoidance_target' => 'テスト',
        ]);

        $response = $this->asMember()->putJson("/api/exposures/{$exposure->id}/hierarchy-items/sync", [
            'items' => [
                ['content' => '場面1', 'sort_order' => 1, 'expected_suds' => 30],
                ['content' => '場面2', 'sort_order' => 2, 'expected_suds' => 60],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'items');
        $this->assertDatabaseCount('exposure_hierarchy_items', 2);
    }

    public function test_add_session_rejects_foreign_hierarchy_item(): void
    {
        $exposure = Exposure::create([
            'member_id' => $this->member->id,
            'avoidance_target' => 'テスト',
        ]);

        $otherExposure = Exposure::create([
            'member_id' => $this->member->id,
            'avoidance_target' => '別のテスト',
        ]);

        $foreignItem = ExposureHierarchyItem::create([
            'exposure_id' => $otherExposure->id,
            'content' => '他人の階段',
            'sort_order' => 1,
        ]);

        $response = $this->asMember()->postJson("/api/exposures/{$exposure->id}/sessions", [
            'hierarchy_item_id' => $foreignItem->id,
            'action_plan' => '実施する',
        ]);

        $response->assertStatus(422);
    }

    public function test_sync_sessions_rejects_foreign_hierarchy_item(): void
    {
        $exposure = Exposure::create([
            'member_id' => $this->member->id,
            'avoidance_target' => 'テスト',
        ]);

        $otherExposure = Exposure::create([
            'member_id' => $this->member->id,
            'avoidance_target' => '別のテスト',
        ]);

        $foreignItem = ExposureHierarchyItem::create([
            'exposure_id' => $otherExposure->id,
            'content' => '他人の階段',
            'sort_order' => 1,
        ]);

        $response = $this->asMember()->putJson("/api/exposures/{$exposure->id}/sessions/sync", [
            'sessions' => [
                [
                    'hierarchy_item_id' => $foreignItem->id,
                    'action_plan' => '実施する',
                ],
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_sync_sessions_in_single_request(): void
    {
        $exposure = Exposure::create([
            'member_id' => $this->member->id,
            'avoidance_target' => 'テスト',
        ]);

        $response = $this->asMember()->putJson("/api/exposures/{$exposure->id}/sessions/sync", [
            'sessions' => [
                [
                    'action_plan' => '1駅だけ乗る',
                    'suds_before' => 80,
                    'suds_peak' => 90,
                    'suds_after' => 60,
                    'reflection' => 'なんとかできた',
                ],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'sessions');
        $this->assertDatabaseCount('exposure_sessions', 1);
    }

    public function test_sync_sessions_reassigns_session_numbers_when_order_changes(): void
    {
        $exposure = Exposure::create([
            'member_id' => $this->member->id,
            'avoidance_target' => 'テスト',
        ]);

        $existingSession = ExposureSession::create([
            'exposure_id' => $exposure->id,
            'session_number' => 1,
            'action_plan' => '既存の計画',
        ]);

        $response = $this->asMember()->putJson("/api/exposures/{$exposure->id}/sessions/sync", [
            'sessions' => [
                [
                    'action_plan' => '新規の計画',
                ],
                [
                    'id' => $existingSession->id,
                    'action_plan' => '既存の計画',
                ],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('sessions.0.session_number', 1);
        $response->assertJsonPath('sessions.1.session_number', 2);

        $this->assertDatabaseHas('exposure_sessions', [
            'id' => $existingSession->id,
            'session_number' => 2,
        ]);
    }

    public function test_update_hierarchy_item_preserves_existing_entity(): void
    {
        $exposure = Exposure::create([
            'member_id' => $this->member->id,
            'avoidance_target' => 'テスト',
        ]);

        $item = ExposureHierarchyItem::create([
            'exposure_id' => $exposure->id,
            'content' => '元の場面',
            'sort_order' => 1,
            'expected_suds' => 40,
        ]);

        $response = $this->asMember()->putJson(
            "/api/exposures/{$exposure->id}/hierarchy-items/{$item->id}",
            [
                'content' => '更新後の場面',
                'sort_order' => 2,
                'expected_suds' => 70,
            ]
        );

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'content' => '更新後の場面',
            'sort_order' => 2,
            'expected_suds' => 70,
        ]);
    }
}
