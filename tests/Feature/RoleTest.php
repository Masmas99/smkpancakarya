<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_default_to_the_staff_role(): void
    {
        $user = User::factory()->create();

        $this->assertSame(UserRole::Staff, $user->fresh()->role);
        $this->assertTrue($user->isStaff());
        $this->assertFalse($user->isAdmin());
    }

    public function test_admin_role_helpers(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertTrue($admin->fresh()->isAdmin());
        $this->assertFalse($admin->isStaff());
        $this->assertTrue($admin->hasRole(UserRole::Admin));
    }

    public function test_users_can_be_filtered_by_role(): void
    {
        User::factory()->admin()->create();
        User::factory()->count(2)->create();

        $this->assertSame(1, User::role(UserRole::Admin)->count());
        $this->assertSame(2, User::role(UserRole::Staff)->count());
    }

    public function test_role_labels_and_options(): void
    {
        $this->assertSame('Administrator', UserRole::Admin->label());
        $this->assertSame(
            ['admin' => 'Administrator', 'staff' => 'Staff'],
            UserRole::options(),
        );
    }
}
