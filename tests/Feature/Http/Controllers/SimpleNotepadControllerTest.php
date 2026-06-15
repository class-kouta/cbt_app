<?php

namespace Tests\Feature\Http\Controllers;

use App\Infrastructure\Database\Models\SimpleNotepad;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimpleNotepadControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_only_authenticated_members_notepads(): void
    {
        $member = Member::factory()->create();
        $otherMember = Member::factory()->create();

        $ownNotepad = SimpleNotepad::create([
            'member_id' => $member->id,
            'content' => '自分の内容',
        ]);

        SimpleNotepad::create([
            'member_id' => $otherMember->id,
            'content' => '他人の内容',
        ]);

        $response = $this->actingAs($member, 'sanctum')->getJson('/api/simple-notepads');

        $response->assertOk();
        $response->assertJsonPath('total', 1);
        $response->assertJsonPath('data.0.id', $ownNotepad->id);
        $response->assertJsonPath('data.0.content', '自分の内容');
    }

    public function test_show_returns_not_found_for_other_members_notepad(): void
    {
        $member = Member::factory()->create();
        $otherMember = Member::factory()->create();

        $otherNotepad = SimpleNotepad::create([
            'member_id' => $otherMember->id,
            'content' => '他人の内容',
        ]);

        $this->actingAs($member, 'sanctum')
            ->getJson("/api/simple-notepads/{$otherNotepad->id}")
            ->assertNotFound();
    }

    public function test_show_returns_own_notepad(): void
    {
        $member = Member::factory()->create();

        $notepad = SimpleNotepad::create([
            'member_id' => $member->id,
            'content' => '自分の内容',
        ]);

        $this->actingAs($member, 'sanctum')
            ->getJson("/api/simple-notepads/{$notepad->id}")
            ->assertOk()
            ->assertJsonPath('id', $notepad->id)
            ->assertJsonPath('content', '自分の内容');
    }
}
