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

    private function createExposureWithHierarchy(string $avoidanceTarget = 'テスト'): array
    {
        $exposure = Exposure::create([
            'member_id' => $this->member->id,
            'avoidance_target' => $avoidanceTarget,
        ]);

        $item = ExposureHierarchyItem::create([
            'exposure_id' => $exposure->id,
            'content' => '場面1',
            'sort_order' => 1,
            'expected_suds' => 50,
        ]);

        return [$exposure, $item];
    }

    private function createSession(
        Exposure $exposure,
        ExposureHierarchyItem $item,
        int $sessionNumber = 1,
        int $sudsAfter = 40,
        ?string $reflection = null
    ): ExposureSession {
        return ExposureSession::create([
            'exposure_id' => $exposure->id,
            'hierarchy_item_id' => $item->id,
            'session_number' => $sessionNumber,
            'suds_after' => $sudsAfter,
            'reflection' => $reflection,
        ]);
    }

    public function test_sessions_returns_records(): void
    {
        [$exposure, $item] = $this->createExposureWithHierarchy('電車が怖い');
        $this->createSession($exposure, $item, 1, 30, 'なんとか乗れた');

        [$exposure2, $item2] = $this->createExposureWithHierarchy('人前で話すのが怖い');
        $this->createSession($exposure2, $item2, 1, 60);

        $response = $this->asMember()->getJson('/api/exposures/sessions');

        $response->assertStatus(200);
        $response->assertJsonPath('total', 2);
    }

    public function test_sessions_filter_by_exposure_id(): void
    {
        [$exposure, $item] = $this->createExposureWithHierarchy('電車が怖い');
        $this->createSession($exposure, $item);

        [$exposure2, $item2] = $this->createExposureWithHierarchy('人前で話す');
        $this->createSession($exposure2, $item2);

        $response = $this->asMember()->getJson("/api/exposures/sessions?exposure_id={$exposure->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('total', 1);
        $response->assertJsonPath('data.0.exposure_id', $exposure->id);
    }

    public function test_sessions_filter_by_exposure_id_without_hierarchy_item_id(): void
    {
        [$exposure, $item] = $this->createExposureWithHierarchy('あたたほやな');
        $this->createSession($exposure, $item, 1, 35);

        $response = $this->asMember()->getJson("/api/exposures/sessions?exposure_id={$exposure->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('total', 1);
        $response->assertJsonPath('data.0.avoidance_target', 'あたたほやな');
    }

    public function test_sessions_filter_by_hierarchy_item_id(): void
    {
        [$exposure, $item] = $this->createExposureWithHierarchy();
        $item2 = ExposureHierarchyItem::create([
            'exposure_id' => $exposure->id,
            'content' => '場面2',
            'sort_order' => 2,
        ]);

        $this->createSession($exposure, $item, 1);
        $this->createSession($exposure, $item2, 2);

        $response = $this->asMember()->getJson("/api/exposures/sessions?exposure_id={$exposure->id}&hierarchy_item_id={$item->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('total', 1);
        $response->assertJsonPath('data.0.hierarchy_item_id', $item->id);
    }

    public function test_sessions_rejects_hierarchy_filter_without_exposure(): void
    {
        [$exposure, $item] = $this->createExposureWithHierarchy();
        $this->createSession($exposure, $item);

        $response = $this->asMember()->getJson("/api/exposures/sessions?hierarchy_item_id={$item->id}");

        $response->assertStatus(422);
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

    public function test_sync_hierarchy_items_preserves_session_links(): void
    {
        [$exposure, $item] = $this->createExposureWithHierarchy();
        $session = $this->createSession($exposure, $item);

        $response = $this->asMember()->putJson("/api/exposures/{$exposure->id}/hierarchy-items/sync", [
            'items' => [
                [
                    'id' => $item->id,
                    'content' => '更新後の場面',
                    'sort_order' => 1,
                    'expected_suds' => 70,
                ],
            ],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('exposure_sessions', [
            'id' => $session->id,
            'hierarchy_item_id' => $item->id,
        ]);
    }

    public function test_add_session_requires_hierarchy_item_and_suds_after(): void
    {
        [$exposure, $item] = $this->createExposureWithHierarchy();

        $response = $this->asMember()->postJson("/api/exposures/{$exposure->id}/sessions", [
            'hierarchy_item_id' => $item->id,
            'suds_after' => 45,
            'reflection' => 'できた',
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('suds_after', 45);
    }

    public function test_add_session_rejects_foreign_hierarchy_item(): void
    {
        [$exposure] = $this->createExposureWithHierarchy();
        [$otherExposure, $foreignItem] = $this->createExposureWithHierarchy('別のテスト');

        $response = $this->asMember()->postJson("/api/exposures/{$exposure->id}/sessions", [
            'hierarchy_item_id' => $foreignItem->id,
            'suds_after' => 50,
        ]);

        $response->assertStatus(422);
    }

    public function test_add_session_rejects_when_previous_reflection_is_incomplete(): void
    {
        [$exposure, $item] = $this->createExposureWithHierarchy();
        $this->createSession($exposure, $item, 1, 40);

        $response = $this->asMember()->postJson("/api/exposures/{$exposure->id}/sessions", [
            'hierarchy_item_id' => $item->id,
            'suds_after' => 30,
        ]);

        $response->assertStatus(422);
    }

    public function test_add_session_rejects_invalid_suds_step(): void
    {
        [$exposure, $item] = $this->createExposureWithHierarchy();

        $response = $this->asMember()->postJson("/api/exposures/{$exposure->id}/sessions", [
            'hierarchy_item_id' => $item->id,
            'suds_after' => 37,
        ]);

        $response->assertStatus(422);
    }

    public function test_show_session(): void
    {
        [$exposure, $item] = $this->createExposureWithHierarchy();
        $session = $this->createSession($exposure, $item, 1, 55, '振り返り');

        $response = $this->asMember()->getJson("/api/exposures/sessions/{$session->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('exposure_id', $exposure->id);
        $response->assertJsonPath('hierarchy_item_id', $item->id);
        $response->assertJsonPath('session_number', 1);
        $response->assertJsonPath('suds_after', 55);
        $response->assertJsonPath('reflection', '振り返り');
        $response->assertJsonPath('avoidance_target', $exposure->avoidance_target);
        $response->assertJsonPath('hierarchy_item_content', $item->content);
    }

    public function test_update_session(): void
    {
        [$exposure, $item] = $this->createExposureWithHierarchy();
        $session = $this->createSession($exposure, $item, 1, 50, '旧い振り返り');

        $response = $this->asMember()->putJson("/api/exposures/{$exposure->id}/sessions/{$session->id}", [
            'hierarchy_item_id' => $item->id,
            'suds_after' => 35,
            'reflection' => '新しい振り返り',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('suds_after', 35);
        $response->assertJsonPath('reflection', '新しい振り返り');
    }

    public function test_delete_session(): void
    {
        [$exposure, $item] = $this->createExposureWithHierarchy();
        $session = $this->createSession($exposure, $item);

        $this->asMember()->deleteJson("/api/exposures/{$exposure->id}/sessions/{$session->id}")
            ->assertStatus(204);

        $this->assertDatabaseMissing('exposure_sessions', ['id' => $session->id]);
    }

    public function test_show_session_returns_not_found_for_other_members_session(): void
    {
        [$exposure, $item] = $this->createExposureWithHierarchy();
        $session = $this->createSession($exposure, $item);

        $otherMember = Member::factory()->create();

        $this->actingAs($otherMember, 'sanctum')
            ->getJson("/api/exposures/sessions/{$session->id}")
            ->assertStatus(404);
    }

    public function test_show_exposure_returns_not_found_for_other_members_exposure(): void
    {
        $exposure = Exposure::create([
            'member_id' => $this->member->id,
            'avoidance_target' => '他人のデータ',
        ]);

        $otherMember = Member::factory()->create();

        $this->actingAs($otherMember, 'sanctum')
            ->getJson("/api/exposures/{$exposure->id}")
            ->assertStatus(404);
    }

    public function test_sync_sessions_can_reorder_without_unique_constraint_violation(): void
    {
        [$exposure, $item] = $this->createExposureWithHierarchy();

        $session1 = ExposureSession::create([
            'exposure_id' => $exposure->id,
            'hierarchy_item_id' => $item->id,
            'session_number' => 1,
            'suds_after' => 30,
        ]);
        $session2 = ExposureSession::create([
            'exposure_id' => $exposure->id,
            'hierarchy_item_id' => $item->id,
            'session_number' => 2,
            'suds_after' => 50,
        ]);

        $response = $this->asMember()->putJson("/api/exposures/{$exposure->id}/sessions/sync", [
            'sessions' => [
                [
                    'id' => $session2->id,
                    'hierarchy_item_id' => $item->id,
                    'suds_after' => 50,
                ],
                [
                    'id' => $session1->id,
                    'hierarchy_item_id' => $item->id,
                    'suds_after' => 30,
                ],
            ],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('exposure_sessions', [
            'id' => $session2->id,
            'session_number' => 1,
        ]);
        $this->assertDatabaseHas('exposure_sessions', [
            'id' => $session1->id,
            'session_number' => 2,
        ]);
    }

    public function test_add_session_assigns_incrementing_session_numbers(): void
    {
        [$exposure, $item] = $this->createExposureWithHierarchy();

        $this->asMember()->postJson("/api/exposures/{$exposure->id}/sessions", [
            'hierarchy_item_id' => $item->id,
            'suds_after' => 40,
            'reflection' => '1回目の振り返り',
        ])->assertStatus(201)->assertJsonPath('session_number', 1);

        $this->asMember()->postJson("/api/exposures/{$exposure->id}/sessions", [
            'hierarchy_item_id' => $item->id,
            'suds_after' => 20,
        ])->assertStatus(201)->assertJsonPath('session_number', 2);
    }

    public function test_export_csv_returns_csv_file(): void
    {
        [$exposure, $item] = $this->createExposureWithHierarchy('CSV出力テスト');
        $this->createSession($exposure, $item, 1, 40, '振り返り');

        $response = $this->asMember()->get('/api/exposures/export/csv');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=utf-8');
        $this->assertStringContainsString('CSV出力テスト', $response->streamedContent());
    }

    public function test_options_returns_lightweight_exposure_list(): void
    {
        Exposure::create([
            'member_id' => $this->member->id,
            'avoidance_target' => 'オプション1',
        ]);
        Exposure::create([
            'member_id' => $this->member->id,
            'avoidance_target' => 'オプション2',
        ]);

        $response = $this->asMember()->getJson('/api/exposures/options');

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['avoidance_target' => 'オプション1']);
        $response->assertJsonMissingPath('data.0.hierarchy_items');
    }
}
