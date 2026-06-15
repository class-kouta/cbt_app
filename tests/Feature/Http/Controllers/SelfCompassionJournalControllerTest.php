<?php

namespace Tests\Feature\Http\Controllers;

use App\Infrastructure\Database\Models\SelfCompassionJournal;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SelfCompassionJournalControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_only_authenticated_members_records(): void
    {
        $member = Member::factory()->create();
        $otherMember = Member::factory()->create();

        $ownRecord = SelfCompassionJournal::create([
            'member_id' => $member->id,
            'difficult_experience' => 'しんどかった',
            'effort_made' => '頑張った',
            'friend_voice' => '大丈夫だよ',
            'word_to_self' => 'よく頑張った',
        ]);

        SelfCompassionJournal::create([
            'member_id' => $otherMember->id,
            'difficult_experience' => '他人の記録',
            'effort_made' => '他人の努力',
            'friend_voice' => '他人の声',
            'word_to_self' => '他人の一言',
        ]);

        $response = $this->actingAs($member, 'sanctum')->getJson('/api/self-compassion-journals');

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonPath('0.id', $ownRecord->id);
        $response->assertJsonPath('0.difficult_experience', 'しんどかった');
        $response->assertJsonPath('0.effort_made', '頑張った');
        $response->assertJsonPath('0.friend_voice', '大丈夫だよ');
        $response->assertJsonPath('0.word_to_self', 'よく頑張った');
    }

    public function test_store_creates_self_compassion_journal(): void
    {
        $member = Member::factory()->create();

        $response = $this->actingAs($member, 'sanctum')->postJson('/api/self-compassion-journals', [
            'difficult_experience' => '仕事で失敗した',
            'effort_made' => '翌日も出勤した',
            'friend_voice' => '誰にでもあるよ',
            'word_to_self' => '自分を認めよう',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('difficult_experience', '仕事で失敗した');
        $response->assertJsonPath('effort_made', '翌日も出勤した');
        $response->assertJsonPath('friend_voice', '誰にでもあるよ');
        $response->assertJsonPath('word_to_self', '自分を認めよう');

        $this->assertDatabaseHas('self_compassion_journals', [
            'member_id' => $member->id,
            'difficult_experience' => '仕事で失敗した',
            'effort_made' => '翌日も出勤した',
            'friend_voice' => '誰にでもあるよ',
            'word_to_self' => '自分を認めよう',
        ]);
    }

    public function test_store_requires_all_fields(): void
    {
        $member = Member::factory()->create();

        $response = $this->actingAs($member, 'sanctum')->postJson('/api/self-compassion-journals', [
            'difficult_experience' => 'しんどかった',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors([
            'effort_made',
            'friend_voice',
            'word_to_self',
        ]);
    }

    public function test_store_rejects_empty_strings(): void
    {
        $member = Member::factory()->create();

        $response = $this->actingAs($member, 'sanctum')->postJson('/api/self-compassion-journals', [
            'difficult_experience' => '',
            'effort_made' => '',
            'friend_voice' => '',
            'word_to_self' => '',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors([
            'difficult_experience',
            'effort_made',
            'friend_voice',
            'word_to_self',
        ]);
    }

    public function test_unauthenticated_user_cannot_access_api(): void
    {
        $this->getJson('/api/self-compassion-journals')->assertUnauthorized();
        $this->postJson('/api/self-compassion-journals', [])->assertUnauthorized();
    }
}
