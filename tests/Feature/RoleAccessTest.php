<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_masyarakat_cannot_access_admin_dashboard(): void
    {
        $this->seed();
        $user = User::where('role', 'masyarakat')->firstOrFail();

        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $this->seed();
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->assertSee('Dashboard', false);
    }
}
