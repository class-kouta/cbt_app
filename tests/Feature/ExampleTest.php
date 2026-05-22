<?php

namespace Tests\Feature;

use App\Models\Member;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_member_can_view_home(): void
    {
        $member = new Member([
            'name' => 'Test Member',
            'email' => 'test@example.com',
        ]);
        $member->id = 1;

        $response = $this->actingAs($member)->get('/');

        $response->assertStatus(200);
    }
}
