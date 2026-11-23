<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_admin_is_shown_the_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Dashboard Admin');
    }

    public function test_staff_is_shown_the_staff_dashboard(): void
    {
        $staff = User::factory()->create();

        $this->actingAs($staff)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Dashboard Staff');
    }

    public function test_admin_can_open_the_admin_area(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin/dashboard')->assertOk();
    }

    public function test_staff_cannot_open_the_admin_area(): void
    {
        $staff = User::factory()->create();

        $this->actingAs($staff)->get('/admin/dashboard')->assertForbidden();
    }

    public function test_staff_can_open_the_staff_area(): void
    {
        $staff = User::factory()->create();

        $this->actingAs($staff)->get('/staff/dashboard')->assertOk();
    }

    public function test_admin_cannot_open_the_staff_area(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/staff/dashboard')->assertForbidden();
    }
}
