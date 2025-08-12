<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_is_redirected_to_login()
    {
        $response = $this->get('/citizens');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_citizens_page()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/citizens');
        $response->assertOk();
    }

    public function test_admin_can_access_dashboard()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertOk();
    }

    public function test_registrar_cannot_access_dashboard()
    {
        $registrar = User::factory()->create(['role' => 'registrar']);
        $response = $this->actingAs($registrar)->get('/dashboard');
        $response->assertForbidden();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(302);
    }
}
