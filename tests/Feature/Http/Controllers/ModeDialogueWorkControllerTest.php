<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\ModeCategory;
use App\Infrastructure\Database\Models\DialogueWork;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModeDialogueWorkControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_only_authenticated_members_mode_dialogue_works(): void
    {
        $member = Member::factory()->create();
        $otherMember = Member::factory()->create();

        DialogueWork::create([
            'member_id' => $member->id,
            'type' => 'mode',
            'content' => 'my mode work',
            'mode_category' => ModeCategory::VULNERABLE_CHILD->value,
            'mode_name' => 'Punitive Parent',
        ]);

        DialogueWork::create([
            'member_id' => $member->id,
            'type' => 'healthy',
            'content' => 'my healthy work',
        ]);

        DialogueWork::create([
            'member_id' => $otherMember->id,
            'type' => 'mode',
            'content' => 'other mode work',
            'mode_category' => ModeCategory::ANGRY_ADULT->value,
            'mode_name' => 'Demanding Parent',
        ]);

        $response = $this->actingAs($member, 'sanctum')->getJson('/api/mode-dialogue-works');

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonPath('0.content', 'my mode work');
    }

    public function test_show_returns_404_when_dialogue_work_is_not_mode_type(): void
    {
        $member = Member::factory()->create();

        $healthyDialogueWork = DialogueWork::create([
            'member_id' => $member->id,
            'type' => 'healthy',
            'content' => 'healthy content',
        ]);

        $response = $this->actingAs($member, 'sanctum')
            ->getJson("/api/mode-dialogue-works/{$healthyDialogueWork->id}");

        $response->assertNotFound();
    }

    public function test_update_returns_404_when_dialogue_work_is_not_mode_type(): void
    {
        $member = Member::factory()->create();

        $healthyDialogueWork = DialogueWork::create([
            'member_id' => $member->id,
            'type' => 'healthy',
            'content' => 'healthy content',
        ]);

        $response = $this->actingAs($member, 'sanctum')->putJson(
            "/api/mode-dialogue-works/{$healthyDialogueWork->id}",
            ['content' => 'updated content']
        );

        $response->assertNotFound();
    }
}
