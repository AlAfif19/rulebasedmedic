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
            ->assertSee('Tentang', false)
            ->assertSee('Aturan', false)
            ->assertSee('Efek', false)
            ->assertSee('Peringatan', false)
            ->assertSee('Interaksi', false)
            ->assertSee('Bentuk', false)
            ->assertSee('Produsen', false);
    }

    public function test_main_reference_asset_is_used_on_public_screens(): void
    {
        $this->seed();

        $this->get(route('landing'))->assertOk()->assertSee('assets/images/medical-hero.svg', false);
        $this->get(route('login'))->assertOk()->assertSee('assets/images/medical-hero.svg', false);
        $this->get(route('register'))->assertOk()->assertSee('assets/images/medical-hero.svg', false);
        $this->get(route('information'))->assertOk()->assertSee('assets/images/medical-hero.svg', false);
    }
}
