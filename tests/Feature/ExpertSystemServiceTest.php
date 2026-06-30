<?php

namespace Tests\Feature;

use App\Models\Rule;
use App\Services\ExpertSystemService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpertSystemServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_forward_chaining_returns_influenza_ringan_for_rule_r001(): void
    {
        $this->seed();

        $result = app(ExpertSystemService::class)->analyze(['G001', 'G009', 'G011'], 'forward');

        $this->assertSame('forward', $result['method']);
        $this->assertSame('P001', $result['disease']->code);
        $this->assertSame(['G001', 'G009', 'G011'], $result['matched_rule']['matched_symptoms']);
        $this->assertGreaterThan(0, $result['confidence_score']);
        $this->assertNotEmpty($result['medicines']);
    }

    public function test_backward_chaining_scores_partial_goal_match(): void
    {
        $this->seed();

        $result = app(ExpertSystemService::class)->analyze(['G001', 'G009'], 'backward');

        $this->assertSame('backward', $result['method']);
        $this->assertSame('P001', $result['disease']->code);
        $this->assertContains('G011', $result['matched_rule']['missing_symptoms']);
        $this->assertGreaterThan(0, $result['confidence_score']);
        $this->assertLessThan(100, $result['confidence_score']);
    }

    public function test_parallel_analysis_runs_all_reasoning_methods_together(): void
    {
        $this->seed();

        $result = app(ExpertSystemService::class)->analyze(['G001', 'G009', 'G011']);

        $this->assertSame('parallel', $result['method']);
        $this->assertSame(['rule_based', 'forward_chaining', 'backward_chaining', 'certainty_factor'], array_keys($result['method_scores']));
        $this->assertSame($result['confidence_score'], $result['matched_rule']['parallel_score']);
        $this->assertGreaterThan(0, $result['method_scores']['rule_based']);
        $this->assertGreaterThan(0, $result['method_scores']['forward_chaining']);
        $this->assertGreaterThan(0, $result['method_scores']['backward_chaining']);
        $this->assertGreaterThan(0, $result['method_scores']['certainty_factor']);
    }

    public function test_twenty_representative_complete_symptom_cases_are_safe(): void
    {
        $this->seed();

        $service = app(ExpertSystemService::class);
        $rules = Rule::query()->with('disease')->where('is_active', true)->orderBy('code')->take(20)->get();

        $this->assertCount(20, $rules);

        foreach ($rules as $rule) {
            $result = $service->analyze($rule->symptom_codes);

            $this->assertSame('parallel', $result['method'], "Rule {$rule->code} should use parallel analysis.");
            $this->assertSame($rule->disease->code, $result['disease']->code, "Rule {$rule->code} should diagnose {$rule->disease->code}.");
            $this->assertTrue($result['matched_rule']['is_exact'], "Rule {$rule->code} should be an exact symptom match.");
            $this->assertGreaterThanOrEqual(70, $result['confidence_score'], "Rule {$rule->code} confidence should be safe.");
            $this->assertSame(['rule_based', 'forward_chaining', 'backward_chaining', 'certainty_factor'], array_keys($result['method_scores']));
            $this->assertNotEmpty($result['medicines'], "Rule {$rule->code} should return medicine recommendations.");

            foreach ($result['method_scores'] as $score) {
                $this->assertGreaterThanOrEqual(0, $score);
                $this->assertLessThanOrEqual(100, $score);
            }
        }
    }

    public function test_unmatched_symptoms_return_safe_message(): void
    {
        $this->seed();

        $result = app(ExpertSystemService::class)->analyze(['G007']);

        $this->assertNull($result['disease']);
        $this->assertSame(0, $result['confidence_score']);
        $this->assertSame([
            'rule_based' => 0,
            'forward_chaining' => 0,
            'backward_chaining' => 0,
            'certainty_factor' => 0,
        ], $result['method_scores']);
        $this->assertSame('Belum ditemukan aturan yang sesuai. Silakan pilih gejala yang lebih spesifik atau hubungi apoteker.', $result['message']);
    }
}
