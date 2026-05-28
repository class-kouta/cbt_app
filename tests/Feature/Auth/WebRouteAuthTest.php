<?php

namespace Tests\Feature\Auth;

use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebRouteAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_home_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_member_can_access_home(): void
    {
        $member = Member::factory()->create();

        $response = $this->actingAs($member)->get('/');

        $response->assertOk();
    }

    public function test_guest_can_access_login_page(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
    }

    public function test_authenticated_member_is_redirected_from_login_to_home(): void
    {
        $member = Member::factory()->create();

        $response = $this->actingAs($member)->get('/login');

        $response->assertRedirect(route('home'));
    }

    public function test_guest_is_redirected_from_protected_page_to_login(): void
    {
        $response = $this->get('/columns');

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_member_can_access_protected_page(): void
    {
        $member = Member::factory()->create();

        $response = $this->actingAs($member)->get('/columns');

        $response->assertOk();
    }

    public function test_guest_is_redirected_from_verify_email_to_login(): void
    {
        $response = $this->get('/verify-email');

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_member_can_access_verify_email(): void
    {
        $member = Member::factory()->unverified()->create();

        $response = $this->actingAs($member)->get('/verify-email');

        $response->assertOk();
    }

}

