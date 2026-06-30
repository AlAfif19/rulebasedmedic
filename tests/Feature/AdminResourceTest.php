<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_gejala_resource(): void
    {
        $this->seed();
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.resource.index', 'gejala'))
            ->assertOk()
            ->assertSee('Data Gejala', false)
            ->assertSee('G001', false);
    }

    public function test_admin_can_filter_gejala_by_search_query(): void
    {
        $this->seed();
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.resource.index', ['resource' => 'gejala', 'q' => 'Demam']))
            ->assertOk()
            ->assertSee('Demam', false)
            ->assertDontSee('Batuk Kering', false);
    }
}
