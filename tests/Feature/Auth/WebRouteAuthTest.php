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

    public function test_login_page_shows_test_accounts_in_staging(): void
    {
        config(['app.env' => 'staging']);

        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('テストアカウント（本番以外）');
        $response->assertSee('フォームに入力');
        $response->assertSee('ff03csm26test1@example.com');
        $response->assertSee('ff03csm26test2@example.com');
        $response->assertSee('testtesttest');
        $response->assertDontSee('コピー');
    }

    public function test_login_page_hides_test_accounts_in_production(): void
    {
        config(['app.env' => 'production']);

        $response = $this->get('/login');

        $response->assertOk();
        $response->assertDontSee('テストアカウント（本番以外）');
        $response->assertDontSee('ff03csm26test1@example.com');
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

    public function test_unverified_member_is_redirected_from_home_to_verify_email(): void
    {
        $member = Member::factory()->unverified()->create();

        $response = $this->actingAs($member)->get('/');

        $response->assertRedirect(route('verification.notice'));
    }

    public function test_unverified_member_is_redirected_from_protected_page_to_verify_email(): void
    {
        $member = Member::factory()->unverified()->create();

        $response = $this->actingAs($member)->get('/columns');

        $response->assertRedirect(route('verification.notice'));
    }

    public function test_unverified_member_cannot_access_protected_api(): void
    {
        $member = Member::factory()->unverified()->create();

        $response = $this->actingAs($member, 'sanctum')->getJson('/api/columns');

        $response->assertForbidden();
    }

    public function test_verified_member_is_redirected_from_verify_email_to_home(): void
    {
        $member = Member::factory()->create();

        $response = $this->actingAs($member)->get('/verify-email');

        $response->assertRedirect(route('home'));
    }

}

