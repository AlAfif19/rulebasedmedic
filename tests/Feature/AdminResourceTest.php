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

    public function test_admin_resource_search_auto_submits_without_enter(): void
    {
        $this->seed();
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.resource.index', 'gejala'))
            ->assertOk()
            ->assertSee('data-auto-submit-search', false)
            ->assertSee('data-search-delay="900"', false)
            ->assertSee('placeholder="Cari data atau kode"', false);
    }

    public function test_admin_sidebar_can_be_minimized(): void
    {
        $this->seed();
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('data-admin-shell', false)
            ->assertSee('data-admin-sidebar', false)
            ->assertSee('data-admin-main', false)
            ->assertSee('data-admin-sidebar-toggle', false)
            ->assertSee('data-admin-sidebar-label', false)
            ->assertSee('aria-label="Minimize sidebar"', false);
    }

    public function test_admin_gejala_index_shows_body_location_and_description_columns(): void
    {
        $this->seed();
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.resource.index', 'gejala'))
            ->assertOk()
            ->assertSee('Lokasi Tubuh', false)
            ->assertSee('Deskripsi', false)
            ->assertSee('Gejala Demam Tinggi', false);
    }

    public function test_admin_medicine_index_shows_image_and_price_columns(): void
    {
        $this->seed();
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.resource.index', 'obat'))
            ->assertOk()
            ->assertSee('Gambar', false)
            ->assertSee('Harga', false)
            ->assertSee('Preview obat', false)
            ->assertSee('Rp', false);
    }

    public function test_admin_can_update_medicine_price(): void
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
            'price' => 12500,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.resource.index', 'obat'));
        $this->assertDatabaseHas('medicines', [
            'id' => $medicine->id,
            'price' => 12500,
        ]);
    }

    public function test_admin_topbar_searches_medicine_data(): void
    {
        $this->seed();
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('action="'.route('admin.resource.index', 'obat').'"', false)
            ->assertSee('name="q"', false)
            ->assertSee('data-admin-global-search', false);
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
            'price' => $medicine->price,
            'image_file' => UploadedFile::fake()->image('obat-baru.png', 1600, 1200),
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.resource.index', 'obat'));
        $medicine->refresh();

        $this->assertStringStartsWith('assets/uploads/medicines/', $medicine->image_path);
        $this->assertStringEndsWith('.webp', $medicine->image_path);
        $this->assertTrue(File::exists(public_path($medicine->image_path)));
        $this->assertLessThan(300 * 1024, File::size(public_path($medicine->image_path)));

        [$width, $height] = getimagesize(public_path($medicine->image_path));
        $this->assertLessThanOrEqual(640, max($width, $height));

        File::delete(public_path($medicine->image_path));
    }

    public function test_medicine_upload_form_shows_loading_state(): void
    {
        $this->seed();
        $admin = User::where('role', 'admin')->firstOrFail();
        $medicine = Medicine::firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.resource.edit', ['resource' => 'obat', 'id' => $medicine->id]))
            ->assertOk()
            ->assertSee('data-upload-form', false)
            ->assertSee('data-upload-submit', false)
            ->assertSee('data-upload-status', false)
            ->assertSee('Mengunggah gambar obat...', false);
    }
}
