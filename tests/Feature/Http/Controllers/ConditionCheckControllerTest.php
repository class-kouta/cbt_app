<?php

namespace Tests\Feature\Http\Controllers;

use App\Infrastructure\Database\Models\ConditionCheck;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConditionCheckControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_only_authenticated_members_records(): void
    {
        $member = Member::factory()->create();
        $otherMember = Member::factory()->create();

        $ownRecord = ConditionCheck::create([
            'member_id' => $member->id,
            'mood' => 1,
            'fatigue' => 2,
            'anxiety' => 1,
            'sleepiness' => 2,
            'physical_condition' => 1,
            'memo' => '自分のメモ',
        ]);

        ConditionCheck::create([
            'member_id' => $otherMember->id,
            'mood' => 5,
            'fatigue' => 5,
            'anxiety' => 5,
            'sleepiness' => 5,
            'physical_condition' => 5,
            'memo' => '他人のメモ',
        ]);

        $response = $this->actingAs($member, 'sanctum')->getJson('/api/condition-checks');

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonPath('0.id', $ownRecord->id);
        $response->assertJsonPath('0.memo', '自分のメモ');
    }

    public function test_store_creates_condition_check(): void
    {
        $member = Member::factory()->create();

        $response = $this->actingAs($member, 'sanctum')->postJson('/api/condition-checks', [
            'mood' => 1,
            'fatigue' => 2,
            'anxiety' => 1,
            'sleepiness' => 3,
            'physical_condition' => 2,
            'memo' => '今日はまあまあ',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('mood', 1);
        $response->assertJsonPath('memo', '今日はまあまあ');

        $this->assertDatabaseHas('condition_checks', [
            'member_id' => $member->id,
            'mood' => 1,
            'memo' => '今日はまあまあ',
        ]);
    }

    public function test_store_requires_all_rating_fields(): void
    {
        $member = Member::factory()->create();

        $this->actingAs($member, 'sanctum')
            ->postJson('/api/condition-checks', [
                'mood' => 1,
                'fatigue' => 2,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['anxiety', 'sleepiness', 'physical_condition']);
    }

    public function test_show_returns_not_found_for_other_members_record(): void
    {
        $member = Member::factory()->create();
        $otherMember = Member::factory()->create();

        $otherRecord = ConditionCheck::create([
            'member_id' => $otherMember->id,
            'mood' => 3,
            'fatigue' => 3,
            'anxiety' => 3,
            'sleepiness' => 3,
            'physical_condition' => 3,
        ]);

        $this->actingAs($member, 'sanctum')
            ->getJson("/api/condition-checks/{$otherRecord->id}")
            ->assertNotFound();
    }

    public function test_show_returns_own_record(): void
    {
        $member = Member::factory()->create();

        $record = ConditionCheck::create([
            'member_id' => $member->id,
            'mood' => 2,
            'fatigue' => 3,
            'anxiety' => 1,
            'sleepiness' => 2,
            'physical_condition' => 2,
            'memo' => 'テスト',
        ]);

        $this->actingAs($member, 'sanctum')
            ->getJson("/api/condition-checks/{$record->id}")
            ->assertOk()
            ->assertJsonPath('id', $record->id)
            ->assertJsonPath('mood', 2)
            ->assertJsonPath('memo', 'テスト');
    }

    public function test_update_updates_own_record(): void
    {
        $member = Member::factory()->create();

        $record = ConditionCheck::create([
            'member_id' => $member->id,
            'mood' => 3,
            'fatigue' => 3,
            'anxiety' => 3,
            'sleepiness' => 3,
            'physical_condition' => 3,
            'memo' => null,
        ]);

        $this->actingAs($member, 'sanctum')
            ->putJson("/api/condition-checks/{$record->id}", [
                'mood' => 1,
                'fatigue' => 1,
                'anxiety' => 1,
                'sleepiness' => 1,
                'physical_condition' => 1,
                'memo' => '更新後',
            ])
            ->assertOk()
            ->assertJsonPath('mood', 1)
            ->assertJsonPath('memo', '更新後');
    }

    public function test_update_returns_not_found_for_other_members_record(): void
    {
        $member = Member::factory()->create();
        $otherMember = Member::factory()->create();

        $otherRecord = ConditionCheck::create([
            'member_id' => $otherMember->id,
            'mood' => 3,
            'fatigue' => 3,
            'anxiety' => 3,
            'sleepiness' => 3,
            'physical_condition' => 3,
        ]);

        $this->actingAs($member, 'sanctum')
            ->putJson("/api/condition-checks/{$otherRecord->id}", [
                'mood' => 1,
                'fatigue' => 1,
                'anxiety' => 1,
                'sleepiness' => 1,
                'physical_condition' => 1,
            ])
            ->assertNotFound();
    }

    public function test_destroy_deletes_own_record(): void
    {
        $member = Member::factory()->create();

        $record = ConditionCheck::create([
            'member_id' => $member->id,
            'mood' => 3,
            'fatigue' => 3,
            'anxiety' => 3,
            'sleepiness' => 3,
            'physical_condition' => 3,
        ]);

        $this->actingAs($member, 'sanctum')
            ->deleteJson("/api/condition-checks/{$record->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('condition_checks', ['id' => $record->id]);
    }

    public function test_destroy_returns_not_found_for_other_members_record(): void
    {
        $member = Member::factory()->create();
        $otherMember = Member::factory()->create();

        $otherRecord = ConditionCheck::create([
            'member_id' => $otherMember->id,
            'mood' => 3,
            'fatigue' => 3,
            'anxiety' => 3,
            'sleepiness' => 3,
            'physical_condition' => 3,
        ]);

        $this->actingAs($member, 'sanctum')
            ->deleteJson("/api/condition-checks/{$otherRecord->id}")
            ->assertNotFound();

        $this->assertDatabaseHas('condition_checks', ['id' => $otherRecord->id]);
    }
}
