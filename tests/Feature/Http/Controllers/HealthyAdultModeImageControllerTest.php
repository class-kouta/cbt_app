<?php

namespace Tests\Feature\Http\Controllers;

use App\Infrastructure\Database\Models\HealthyAdultModeImage;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthyAdultModeImageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_returns_404_for_another_members_image(): void
    {
        $member = Member::factory()->create();
        $otherMember = Member::factory()->create();

        $otherImage = HealthyAdultModeImage::create([
            'member_id' => $otherMember->id,
            'content' => '他人のイメージ',
        ]);

        $this->actingAs($member, 'sanctum')
            ->putJson("/api/healthy-adult-mode-images/{$otherImage->id}", [
                'content' => '更新内容',
            ])
            ->assertNotFound();

        $this->assertDatabaseHas('healthy_adult_mode_images', [
            'id' => $otherImage->id,
            'member_id' => $otherMember->id,
            'content' => '他人のイメージ',
        ]);
    }
}
