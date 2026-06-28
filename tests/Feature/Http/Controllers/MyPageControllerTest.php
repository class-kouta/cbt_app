<?php

namespace Tests\Feature\Http\Controllers;

use App\Infrastructure\Database\Models\Column;
use App\Infrastructure\Database\Models\Coping;
use App\Infrastructure\Database\Models\ProblemSolving;
use App\Infrastructure\Database\Models\ProblemSolvingPlan;
use App\Infrastructure\Database\Models\WritingDisclosure;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyPageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_today_activities_returns_only_authenticated_members_records_for_today(): void
    {
        $member = Member::factory()->create();
        $otherMember = Member::factory()->create();

        Column::create([
            'member_id' => $member->id,
            'situation' => '今日の出来事1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Column::create([
            'member_id' => $member->id,
            'situation' => '今日の出来事2',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Column::create([
            'member_id' => $otherMember->id,
            'situation' => '他人の出来事',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Column::create([
            'member_id' => $member->id,
            'situation' => '昨日の出来事',
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        Coping::create([
            'member_id' => $member->id,
            'content' => '深呼吸',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        WritingDisclosure::create([
            'member_id' => $otherMember->id,
            'content' => '他人の筆記開示',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($member, 'sanctum')->getJson('/api/mypage/today-activities');

        $response->assertOk();
        $response->assertJsonPath('has_activities', true);
        $response->assertJsonPath('date', now()->toDateString());
        $response->assertJsonCount(2, 'activities');

        $messages = collect($response->json('activities'))->pluck('message')->all();
        $this->assertContains('コラム法を2件作成しました', $messages);
        $this->assertContains('コーピングを1件作成しました', $messages);
        $this->assertNotContains('筆記開示を1件作成しました', $messages);
    }

    public function test_today_activities_includes_joined_tables_via_union_all(): void
    {
        $member = Member::factory()->create();

        $problemSolving = ProblemSolving::create([
            'member_id' => $member->id,
            'problem_situation' => '仕事のストレス',
            'improved_image' => null,
        ]);

        ProblemSolvingPlan::create([
            'problem_solving_id' => $problemSolving->id,
            'plan_number' => 1,
            'action_plan' => '朝散歩する',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($member, 'sanctum')->getJson('/api/mypage/today-activities');

        $response->assertOk();
        $messages = collect($response->json('activities'))->pluck('message')->all();
        $this->assertContains('実行計画を1件作成しました', $messages);
    }

    public function test_today_activities_returns_empty_when_no_records_for_today(): void
    {
        $member = Member::factory()->create();

        Column::create([
            'member_id' => $member->id,
            'situation' => '昨日の出来事',
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($member, 'sanctum')->getJson('/api/mypage/today-activities');

        $response->assertOk();
        $response->assertJsonPath('has_activities', false);
        $response->assertJsonPath('activities', []);
    }

    public function test_today_activities_requires_authentication(): void
    {
        $response = $this->getJson('/api/mypage/today-activities');

        $response->assertUnauthorized();
    }

    public function test_mypage_web_route_requires_authentication(): void
    {
        $response = $this->get('/mypage');

        $response->assertRedirect('/login');
    }

    public function test_mypage_web_route_is_accessible_for_verified_members(): void
    {
        $member = Member::factory()->create();

        $response = $this->actingAs($member)->get('/mypage');

        $response->assertOk();
        $response->assertViewIs('mypage');
    }
}
