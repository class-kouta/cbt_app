<?php

namespace Tests\Feature\Http\Controllers;

use App\Infrastructure\Database\Models\StressPersonEncyclopedia;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StressPersonEncyclopediaControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_only_authenticated_members_records(): void
    {
        $member = Member::factory()->create();
        $otherMember = Member::factory()->create();

        $ownRecord = StressPersonEncyclopedia::create([
            'member_id' => $member->id,
            'name' => 'Aさん',
            'relationship' => '職場の上司',
        ]);

        StressPersonEncyclopedia::create([
            'member_id' => $otherMember->id,
            'name' => '他人の記録',
        ]);

        $response = $this->actingAs($member, 'sanctum')->getJson('/api/stress-person-encyclopedias');

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonPath('0.id', $ownRecord->id);
        $response->assertJsonPath('0.name', 'Aさん');
        $response->assertJsonPath('0.relationship', '職場の上司');
    }

    public function test_store_creates_stress_person_encyclopedia_with_only_name(): void
    {
        $member = Member::factory()->create();

        $response = $this->actingAs($member, 'sanctum')->postJson('/api/stress-person-encyclopedias', [
            'name' => 'Bさん',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('name', 'Bさん');
        $response->assertJsonPath('relationship', null);

        $this->assertDatabaseHas('stress_person_encyclopedias', [
            'member_id' => $member->id,
            'name' => 'Bさん',
            'relationship' => null,
        ]);
    }

    public function test_store_creates_stress_person_encyclopedia_with_all_fields(): void
    {
        $member = Member::factory()->create();

        $response = $this->actingAs($member, 'sanctum')->postJson('/api/stress-person-encyclopedias', [
            'name' => 'Cさん',
            'relationship' => '大学の同級生',
            'difficult_traits' => '批判的',
            'my_reaction' => '緊張する',
            'coping_strategy' => '深呼吸する',
            'notes' => '月曜がつらい',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('name', 'Cさん');
        $response->assertJsonPath('difficult_traits', '批判的');

        $this->assertDatabaseHas('stress_person_encyclopedias', [
            'member_id' => $member->id,
            'name' => 'Cさん',
            'coping_strategy' => '深呼吸する',
        ]);
    }

    public function test_store_requires_name(): void
    {
        $member = Member::factory()->create();

        $response = $this->actingAs($member, 'sanctum')->postJson('/api/stress-person-encyclopedias', [
            'relationship' => '職場の同僚',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_store_rejects_empty_name(): void
    {
        $member = Member::factory()->create();

        $response = $this->actingAs($member, 'sanctum')->postJson('/api/stress-person-encyclopedias', [
            'name' => '',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_unauthenticated_user_cannot_access_api(): void
    {
        $this->getJson('/api/stress-person-encyclopedias')->assertUnauthorized();
        $this->postJson('/api/stress-person-encyclopedias', [])->assertUnauthorized();
    }

    public function test_show_returns_entry_for_authenticated_member(): void
    {
        $member = Member::factory()->create();
        $entry = StressPersonEncyclopedia::create([
            'member_id' => $member->id,
            'name' => 'Dさん',
            'my_reaction' => 'イライラする',
        ]);

        $response = $this->actingAs($member, 'sanctum')->getJson("/api/stress-person-encyclopedias/{$entry->id}");

        $response->assertOk();
        $response->assertJsonPath('id', $entry->id);
        $response->assertJsonPath('name', 'Dさん');
        $response->assertJsonPath('my_reaction', 'イライラする');
    }

    public function test_show_returns_404_for_other_members_entry(): void
    {
        $member = Member::factory()->create();
        $otherMember = Member::factory()->create();

        $entry = StressPersonEncyclopedia::create([
            'member_id' => $otherMember->id,
            'name' => '他人の記録',
        ]);

        $this->actingAs($member, 'sanctum')
            ->getJson("/api/stress-person-encyclopedias/{$entry->id}")
            ->assertNotFound();
    }

    public function test_update_updates_entry(): void
    {
        $member = Member::factory()->create();
        $entry = StressPersonEncyclopedia::create([
            'member_id' => $member->id,
            'name' => 'Eさん',
            'relationship' => '同僚',
        ]);

        $response = $this->actingAs($member, 'sanctum')->putJson("/api/stress-person-encyclopedias/{$entry->id}", [
            'name' => 'Eさん（更新）',
            'relationship' => '元同僚',
            'notes' => '転職した',
        ]);

        $response->assertOk();
        $response->assertJsonPath('name', 'Eさん（更新）');
        $response->assertJsonPath('notes', '転職した');

        $this->assertDatabaseHas('stress_person_encyclopedias', [
            'id' => $entry->id,
            'name' => 'Eさん（更新）',
        ]);
    }

    public function test_destroy_deletes_entry(): void
    {
        $member = Member::factory()->create();
        $entry = StressPersonEncyclopedia::create([
            'member_id' => $member->id,
            'name' => 'Fさん',
        ]);

        $this->actingAs($member, 'sanctum')
            ->deleteJson("/api/stress-person-encyclopedias/{$entry->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('stress_person_encyclopedias', [
            'id' => $entry->id,
        ]);
    }
}
