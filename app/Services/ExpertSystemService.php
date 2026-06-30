<?php

namespace App\Services;

use App\Models\Disease;
use App\Models\Medicine;
use App\Models\Rule;
use App\Models\Symptom;
use Illuminate\Support\Collection;

class ExpertSystemService
{
    public function analyze(array $selectedCodes, string $method = 'parallel'): array
    {
        $method = in_array($method, ['forward', 'backward', 'certainty', 'parallel'], true) ? $method : 'parallel';
        $selectedCodes = array_values(array_unique(array_map('strtoupper', $selectedCodes)));
        $rules = Rule::query()->with('disease')->where('is_active', true)->get();
        $symptoms = Symptom::query()->whereIn('code', $selectedCodes)->get()->keyBy('code');

        $ranked = $rules->map(function (Rule $rule) use ($selectedCodes, $symptoms, $method) {
            $required = $this->normalizeCodes($rule->symptom_codes ?? []);
            $matched = array_values(array_intersect($required, $selectedCodes));
            $missing = array_values(array_diff($required, $selectedCodes));
            $matchRatio = count($required) > 0 ? count($matched) / count($required) : 0;
            $certaintyScore = $this->calculateCertaintyFactor($rule, $matched, $required, $symptoms);
            $methodScores = $this->calculateMethodScores($rule, $matched, $missing, $required, $certaintyScore);
            $score = $method === 'parallel'
                ? $this->calculateParallelScore($methodScores)
                : $this->scoreForMethod($method, $methodScores);

            return [
                'rule' => $rule,
                'disease' => $rule->disease,
                'required_symptoms' => $required,
                'matched_symptoms' => $matched,
                'missing_symptoms' => $missing,
                'match_ratio' => round($matchRatio * 100, 2),
                'certainty_factor' => $certaintyScore,
                'method_scores' => $methodScores,
                'parallel_score' => $this->calculateParallelScore($methodScores),
                'score' => $score,
                'is_exact' => count($required) > 0 && count($missing) === 0,
            ];
        })->filter(fn ($item) => $item['match_ratio'] > 0)
          ->sortByDesc(fn ($item) => [$item['is_exact'] ? 1 : 0, $item['score'], $item['match_ratio']])
          ->values();

        $best = $ranked->first();

        if (!$best) {
            return [
                'method' => $method,
                'method_scores' => $this->emptyMethodScores(),
                'selected_symptoms' => $symptoms->values(),
                'disease' => null,
                'medicines' => collect(),
                'confidence_score' => 0,
                'matched_rule' => null,
                'ranked_rules' => collect(),
                'message' => 'Belum ditemukan aturan yang sesuai. Silakan pilih gejala yang lebih spesifik atau hubungi apoteker.',
            ];
        }

        $medicineCodes = $this->normalizeCodes($best['rule']->medicine_codes ?? []);
        $medicines = Medicine::query()->whereIn('code', $medicineCodes)->where('is_active', true)->get();

        return [
            'method' => $method,
            'method_scores' => $best['method_scores'],
            'selected_symptoms' => $symptoms->values(),
            'disease' => $best['disease'],
            'medicines' => $medicines,
            'confidence_score' => $best['score'],
            'matched_rule' => $best,
            'ranked_rules' => $ranked->take(5),
            'message' => $best['is_exact']
                ? 'Rule terpenuhi secara penuh melalui gejala yang dipilih.'
                : 'Rule belum terpenuhi penuh, hasil ditampilkan sebagai kemungkinan awal berdasarkan kecocokan tertinggi.',
        ];
    }

    public function backwardCheck(string $diseaseCode, array $selectedCodes): array
    {
        $disease = Disease::query()->where('code', strtoupper($diseaseCode))->first();
        if (!$disease) {
            return ['status' => false, 'message' => 'Kode penyakit tidak ditemukan.'];
        }

        $rules = Rule::query()->where('disease_id', $disease->id)->where('is_active', true)->get();
        $checks = $rules->map(function (Rule $rule) use ($selectedCodes) {
            $required = $this->normalizeCodes($rule->symptom_codes ?? []);
            return [
                'rule_code' => $rule->code,
                'required' => $required,
                'matched' => array_values(array_intersect($required, $selectedCodes)),
                'missing' => array_values(array_diff($required, $selectedCodes)),
            ];
        });

        return ['status' => true, 'disease' => $disease, 'checks' => $checks];
    }

    private function calculateCertaintyFactor(Rule $rule, array $matched, array $required, Collection $symptoms): float
    {
        if (count($required) === 0) {
            return 0;
        }

        $baseRuleCf = (float) ($rule->cf_value ?: 0.8);
        $weights = collect($matched)->map(function ($code) use ($symptoms) {
            return (float) optional($symptoms->get($code))->weight ?: 0.8;
        });

        $averageWeight = $weights->count() ? $weights->avg() : 0;
        $matchRatio = count($matched) / count($required);

        $cf = $baseRuleCf * $averageWeight * $matchRatio * 100;
        return round(min(100, max(0, $cf)), 2);
    }

    private function calculateMethodScores(Rule $rule, array $matched, array $missing, array $required, float $certaintyScore): array
    {
        if (count($required) === 0) {
            return $this->emptyMethodScores();
        }

        $matchRatio = count($matched) / count($required);
        $ruleCompleteness = count($missing) === 0 ? 100 : $matchRatio * 100;
        $forwardScore = ((float) ($rule->cf_value ?: 0.8)) * $ruleCompleteness;
        $backwardGoalScore = count($missing) === 0 ? 100 : $matchRatio * 100;

        return [
            'rule_based' => round($ruleCompleteness, 2),
            'forward_chaining' => round(min(100, max(0, $forwardScore)), 2),
            'backward_chaining' => round(($certaintyScore * 0.7) + ($backwardGoalScore * 0.3), 2),
            'certainty_factor' => $certaintyScore,
        ];
    }

    private function calculateParallelScore(array $methodScores): float
    {
        return round(array_sum($methodScores) / max(1, count($methodScores)), 2);
    }

    private function scoreForMethod(string $method, array $methodScores): float
    {
        return match ($method) {
            'backward' => $methodScores['backward_chaining'],
            'certainty' => $methodScores['certainty_factor'],
            'forward' => $methodScores['forward_chaining'],
            default => $this->calculateParallelScore($methodScores),
        };
    }

    private function emptyMethodScores(): array
    {
        return [
            'rule_based' => 0,
            'forward_chaining' => 0,
            'backward_chaining' => 0,
            'certainty_factor' => 0,
        ];
    }

    private function normalizeCodes(array|string $codes): array
    {
        if (is_string($codes)) {
            $codes = preg_split('/[,;\s]+/', $codes, -1, PREG_SPLIT_NO_EMPTY);
        }

        return array_values(array_unique(array_map(fn ($code) => strtoupper(trim((string) $code)), $codes)));
    }
}
