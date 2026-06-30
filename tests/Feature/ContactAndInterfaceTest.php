<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactAndInterfaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_information_uses_bhakti_medika_farma_contact_details(): void
    {
        $this->seed();

        $this->get(route('information'))
            ->assertOk()
            ->assertSee('Apotek Bhakti Medika Farma', false)
            ->assertSee('Jl. Moch. Toha No.77', false)
            ->assertSee('+62 822-4674-0801', false)
            ->assertSee('Instagram: @bhaktimedikafarma', false)
            ->assertDontSee("{{ \$contact['instagram'] }}", false)
            ->assertSee('Senin - Sabtu, 08.00 - 20.00', false)
            ->assertSee('https://maps.app.goo.gl/3Jw47coZGatRMsci9', false);
    }

    public function test_masyarakat_navbar_shows_desktop_logout_action(): void
    {
        $this->seed();
        $user = User::where('role', 'masyarakat')->firstOrFail();

        $this->actingAs($user)
            ->get(route('user.dashboard'))
            ->assertOk()
            ->assertSee('Keluar', false);
    }

    public function test_medicine_preview_matches_reference_detail_fields(): void
    {
        $this->seed();
        $user = User::where('role', 'masyarakat')->firstOrFail();

        $response = $this->actingAs($user)->post(route('consultation.diagnose'), [
            'symptoms' => ['G001', 'G009', 'G011'],
        ]);
        $consultation = \App\Models\Consultation::firstOrFail();

        $response->assertRedirect(route('consultation.show', $consultation));
        $this->get(route('consultation.show', $consultation))
            ->assertOk()
            ->assertSee('Informasi Obat', false)
            ->assertSee('Aturan pakai', false)
            ->assertSee('Efek samping', false)
            ->assertSee('Peringatan', false)
            ->assertSee('Bentuk', false)
            ->assertSee('Produsen', false)
            ->assertDontSee('Tentang', false)
            ->assertDontSee('Interaksi', false)
            ->assertDontSee('pb-2">Aturan', false)
            ->assertDontSee('pb-2">Efek', false)
            ->assertDontSee('pb-2">Peringatan', false);
    }

    public function test_main_reference_asset_is_used_on_public_screens(): void
    {
        $this->seed();

        $this->get(route('landing'))->assertOk()->assertSee('assets/images/medical-hero.svg', false);
        $this->get(route('login'))->assertOk()->assertSee('assets/images/medical-hero.svg', false);
        $this->get(route('register'))->assertOk()->assertSee('assets/images/medical-hero.svg', false);
        $this->get(route('information'))->assertOk()->assertSee('assets/images/medical-hero.svg', false);
    }

    public function test_information_page_can_search_medicines(): void
    {
        $this->seed();

        $this->get(route('information', ['q' => 'Becom']))
            ->assertOk()
            ->assertSee('Becom-C', false)
            ->assertDontSee('Acyclovir Salep', false)
            ->assertSee('value="Becom"', false);
    }

    public function test_information_search_uses_live_results(): void
    {
        $this->seed();

        $this->get(route('information'))
            ->assertOk()
            ->assertSee('data-live-search', false)
            ->assertSee('data-live-search-target="#medicine-results"', false)
            ->assertSee('id="medicine-results"', false)
            ->assertSee('action="'.route('information').'"', false);
    }

    public function test_public_and_masyarakat_navbar_searches_information(): void
    {
        $this->seed();
        $user = User::where('role', 'masyarakat')->firstOrFail();

        $this->get(route('landing'))
            ->assertOk()
            ->assertSee('action="'.route('information').'"', false)
            ->assertSee('name="q"', false)
            ->assertSee('data-live-search', false);

        $this->actingAs($user)
            ->get(route('user.dashboard'))
            ->assertOk()
            ->assertSee('action="'.route('information').'"', false)
            ->assertSee('name="q"', false)
            ->assertSee('data-live-search', false);
    }

    public function test_live_search_script_fetches_and_replaces_result_targets(): void
    {
        $script = file_get_contents(resource_path('js/app.js'));

        $this->assertStringContainsString("document.querySelectorAll('[data-live-search]')", $script);
        $this->assertStringContainsString('fetch(', $script);
        $this->assertStringContainsString('DOMParser', $script);
        $this->assertStringContainsString('replaceWith', $script);
    }

    public function test_password_fields_have_visibility_toggle(): void
    {
        $this->seed();
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('data-password-toggle', false)
            ->assertSee('aria-label="Tampilkan password"', false);

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('data-password-toggle', false);

        $this->actingAs($admin)
            ->get(route('admin.resource.create', 'user'))
            ->assertOk()
            ->assertSee('data-password-toggle', false);
    }

    public function test_information_page_uses_working_openstreetmap_embed(): void
    {
        $this->seed();

        $this->get(route('information'))
            ->assertOk()
            ->assertSee('openstreetmap.org/export/embed.html', false)
            ->assertSee('Buka Google Maps', false);
    }

    public function test_consultation_symptom_filters_are_clickable(): void
    {
        $this->seed();
        $user = User::where('role', 'masyarakat')->firstOrFail();

        $this->actingAs($user)
            ->get(route('consultation.index'))
            ->assertOk()
            ->assertSee('data-live-filter-input', false)
            ->assertSee('data-filter-category="all"', false)
            ->assertSee('data-filter-category="Pernapasan dan demam"', false)
            ->assertSee('data-symptom-category="Pernapasan dan demam"', false);
    }

    public function test_selected_symptom_chips_can_remove_symptoms(): void
    {
        $script = file_get_contents(resource_path('js/app.js'));

        $this->assertStringContainsString('data-remove-symptom', $script);
        $this->assertStringContainsString('checkbox.checked = false', $script);
    }
}
