<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AppSetting;
use App\Models\Disease;
use App\Models\Medicine;
use App\Models\Rule;
use App\Models\Symptom;
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

    public function test_admin_resource_search_uses_live_results(): void
    {
        $this->seed();
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.resource.index', 'gejala'))
            ->assertOk()
            ->assertSee('data-live-search', false)
            ->assertSee('data-live-search-target="#admin-resource-results"', false)
            ->assertSee('id="admin-resource-results"', false)
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

    public function test_admin_can_update_each_editable_crud_resource(): void
    {
        $this->seed();
        $admin = User::where('role', 'admin')->firstOrFail();
        $symptom = Symptom::firstOrFail();
        $disease = Disease::firstOrFail();
        $medicine = Medicine::firstOrFail();
        $rule = Rule::firstOrFail();
        $setting = AppSetting::firstOrFail();
        $user = User::where('role', 'masyarakat')->firstOrFail();

        $updates = [
            ['gejala', $symptom->id, [
                'code' => $symptom->code,
                'name' => $symptom->name.' Edit',
                'category' => $symptom->category,
                'description' => $symptom->description,
                'duration' => $symptom->duration,
                'body_location' => $symptom->body_location,
                'frequency' => $symptom->frequency,
                'weight' => $symptom->weight,
                'is_active' => '1',
            ]],
            ['penyakit', $disease->id, [
                'code' => $disease->code,
                'name' => $disease->name.' Edit',
                'severity' => $disease->severity,
                'description' => $disease->description,
                'solution' => $disease->solution,
                'is_active' => '1',
            ]],
            ['obat', $medicine->id, [
                'code' => $medicine->code,
                'disease_id' => $medicine->disease_id,
                'name' => $medicine->name.' Edit',
                'category' => $medicine->category,
                'dosage' => $medicine->dosage,
                'usage_rule' => $medicine->usage_rule,
                'side_effects' => $medicine->side_effects,
                'contraindication' => $medicine->contraindication,
                'warning' => $medicine->warning,
                'description' => $medicine->description,
                'image_path' => $medicine->image_path,
                'price' => $medicine->price,
                'is_active' => '1',
            ]],
            ['rule', $rule->id, [
                'code' => $rule->code,
                'disease_id' => $rule->disease_id,
                'symptom_codes' => implode(', ', $rule->symptom_codes),
                'medicine_codes' => implode(', ', $rule->medicine_codes),
                'cf_value' => $rule->cf_value,
                'method' => 'parallel',
                'description' => $rule->description,
                'is_active' => '1',
            ]],
            ['user', $user->id, [
                'name' => $user->name.' Edit',
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'gender' => $user->gender,
                'phone' => $user->phone,
                'address' => $user->address,
                'password' => '',
            ]],
            ['pengaturan', $setting->id, [
                'key' => $setting->key,
                'value' => $setting->value,
                'group' => $setting->group,
            ]],
        ];

        foreach ($updates as [$resource, $id, $payload]) {
            $this->actingAs($admin)
                ->put(route('admin.resource.update', ['resource' => $resource, 'id' => $id]), $payload)
                ->assertRedirect(route('admin.resource.index', $resource));
        }
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
            ->assertSee('data-live-search', false);
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
            ->assertSee('data-upload-preview', false)
            ->assertSee('data-upload-idle-text', false)
            ->assertSee('data-upload-status', false)
            ->assertSee('Gambar siap dipreview.', false);
    }
}
