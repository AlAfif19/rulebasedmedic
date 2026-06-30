<?php

namespace Tests\Feature;

use App\Models\Consultation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_masyarakat_can_create_consultation_and_see_history(): void
    {
        $this->seed();
        $user = User::where('role', 'masyarakat')->firstOrFail();

        $response = $this->actingAs($user)->post(route('consultation.diagnose'), [
            'symptoms' => ['G001', 'G009', 'G011'],
            'notes' => 'Demam dan pilek sejak pagi',
        ]);

        $consultation = Consultation::firstOrFail();
        $response->assertRedirect(route('consultation.show', $consultation));
        $this->assertSame($user->id, $consultation->user_id);
        $this->assertSame('parallel', $consultation->method);
        $this->assertSame(['G001', 'G009', 'G011'], $consultation->selected_symptom_codes);
        $this->assertNotNull($consultation->result_payload['disease']);
        $this->assertNotEmpty($consultation->result_payload['medicines']);
        $this->assertSame(['rule_based', 'forward_chaining', 'backward_chaining', 'certainty_factor'], array_keys($consultation->result_payload['method_scores']));

        $this->actingAs($user)->get(route('history.index'))->assertOk()->assertSee('Riwayat', false);
    }

    public function test_start_script_does_not_reset_consultation_history(): void
    {
        $script = file_get_contents(base_path('start.sh'));

        $this->assertStringNotContainsString('migrate:fresh', $script);
        $this->assertStringContainsString('php artisan migrate --force', $script);
    }

    public function test_recommendation_medicine_cards_keep_image_and_text_in_separate_columns(): void
    {
        $view = file_get_contents(resource_path('views/user/consultation/show.blade.php'));

        $this->assertStringContainsString('sm:grid-cols-[112px_minmax(0,1fr)]', $view);
        $this->assertStringContainsString('class="h-20 w-28', $view);
        $this->assertStringContainsString('min-w-0', $view);
    }
}
