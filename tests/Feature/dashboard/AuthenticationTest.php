<?php

namespace Tests\Feature\dashboard;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;
    
    private $user;
    private $admin;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_dashboard_not_login_access()
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_dashboard_user_access()
    {
        $response = $this
            ->actingAs($this->user)
            ->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_dashboard_admin_access()
    {
        $response = $this
            ->actingAs($this->admin)
            ->get('/dashboard');

        $response->assertStatus(200);
    }
}
