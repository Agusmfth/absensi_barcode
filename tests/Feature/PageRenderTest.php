<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_admin_pages_render_without_blade_errors(): void
    {
        $this->withoutExceptionHandling();
        $admin = User::factory()->create(['role' => 'admin']);

        foreach ([
            'dashboard',
            'students.index',
            'attendance.scan',
            'attendance.manual',
            'reports.index',
            'teachers.index',
            'classes.index',
            'users.index',
            'settings.index',
            'holidays.index',
        ] as $route) {
            $this->actingAs($admin)->get(route($route))->assertOk();
        }
    }

    public function test_login_page_renders_without_blade_errors(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Masuk ke akun Anda');
    }
}
