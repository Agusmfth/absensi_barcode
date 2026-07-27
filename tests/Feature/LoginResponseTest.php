<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginResponseTest extends TestCase
{
    use RefreshDatabase;

    public function test_ajax_login_only_reports_success_for_valid_credentials(): void
    {
        $user = User::factory()->create(['email' => 'ajax@test.local', 'password' => 'password']);

        $this->postJson('/login', ['login' => $user->email, 'password' => 'salah'])
            ->assertUnprocessable()
            ->assertJson(['message' => 'Email/username atau password salah.']);

        $this->postJson('/login', ['login' => $user->email, 'password' => 'password'])
            ->assertOk()
            ->assertJsonPath('redirect', route('dashboard'));
    }
}
