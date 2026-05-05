<?php

namespace Tests\Feature\Http\Controllers;

use App\Infrastructure\Database\Models\DialogueWork;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModeDialogueWorkControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_only_authenticated_members_mode_records(): void
    {
        $member = Member::factory()->create();
        $otherMember = Member::factory()->create();

        $ownMode = DialogueWork::create([
            'member_id' => $member->id,
            'type' => 'mode',
            'content' => '自分のモードワーク',
            'mode_category' => 'child',
            'mode_name' => 'Vulnerable Child',
        ]);

        DialogueWork::create([
            'member_id' => $member->id,
            'type' => 'healthy',
            'content' => '自分のヘルシー対話',
            'mode_category' => null,
            'mode_name' => null,
        ]);

        DialogueWork::create([
            'member_id' => $otherMember->id,
            'type' => 'mode',
            'content' => '他人のモードワーク',
            'mode_category' => 'coping',
            'mode_name' => 'Detached Protector',
        ]);

        $response = $this->actingAs($member, 'sanctum')->getJson('/api/mode-dialogue-works');

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonPath('0.id', $ownMode->id);
        $response->assertJsonPath('0.content', '自分のモードワーク');
    }

    public function test_store_saves_member_id_for_authenticated_member(): void
    {
        $member = Member::factory()->create();

        $response = $this->actingAs($member, 'sanctum')->postJson('/api/mode-dialogue-works', [
            'content' => '作成テスト',
            'mode_category' => 'child',
            'mode_name' => 'Angry Child',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('dialogue_works', [
            'id' => $response->json('id'),
            'member_id' => $member->id,
            'type' => 'mode',
            'content' => '作成テスト',
            'mode_category' => 'child',
            'mode_name' => 'Angry Child',
        ]);
    }

    public function test_show_update_destroy_return_404_for_non_mode_record(): void
    {
        $member = Member::factory()->create();

        $healthyDialogue = DialogueWork::create([
            'member_id' => $member->id,
            'type' => 'healthy',
            'content' => 'ヘルシー対話',
            'mode_category' => null,
            'mode_name' => null,
        ]);

        $this->actingAs($member, 'sanctum')
            ->getJson("/api/mode-dialogue-works/{$healthyDialogue->id}")
            ->assertNotFound();

        $this->actingAs($member, 'sanctum')
            ->putJson("/api/mode-dialogue-works/{$healthyDialogue->id}", [
                'content' => '更新',
            ])
            ->assertNotFound();

        $this->actingAs($member, 'sanctum')
            ->deleteJson("/api/mode-dialogue-works/{$healthyDialogue->id}")
            ->assertNotFound();
    }
}
