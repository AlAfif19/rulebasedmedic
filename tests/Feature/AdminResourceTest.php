<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Medicine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
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

    public function test_admin_can_upload_medicine_image(): void
    {
        $this->seed();
        $admin = User::where('role', 'admin')->firstOrFail();
        $medicine = Medicine::firstOrFail();

        $response = $this->actingAs($admin)->put(route('admin.resource.update', ['resource' => 'obat', 'id' => $medicine->id]), [
            'code' => $medicine->code,
            'disease_id' => $medicine->disease_id,
            'name' => $medicine->name,
            'category' => $medicine->category,
            'dosage' => $medicine->dosage,
            'usage_rule' => $medicine->usage_rule,
            'side_effects' => $medicine->side_effects,
            'contraindication' => $medicine->contraindication,
            'warning' => $medicine->warning,
            'description' => $medicine->description,
            'image_path' => $medicine->image_path,
            'image_file' => UploadedFile::fake()->image('obat-baru.png', 160, 120),
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.resource.index', 'obat'));
        $medicine->refresh();

        $this->assertStringStartsWith('assets/uploads/medicines/', $medicine->image_path);
        $this->assertTrue(File::exists(public_path($medicine->image_path)));

        File::delete(public_path($medicine->image_path));
    }
}
