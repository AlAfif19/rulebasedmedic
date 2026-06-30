<?php

namespace Database\Seeders;

use App\Models\Disease;
use App\Models\Medicine;
use App\Models\Rule;
use App\Models\Symptom;
use Illuminate\Database\Seeder;

class MedicalKnowledgeSeeder extends Seeder
{
    public function run(): void
    {
        $data = require database_path('data/medical.php');

        foreach ($data['symptoms'] as $symptom) {
            Symptom::updateOrCreate(
                ['code' => $symptom['code']],
                [
                    'name' => $symptom['name'],
                    'category' => $this->categoryForSymptom($symptom['code']),
                    'description' => 'Gejala '.$symptom['name'].' sebagai fakta awal pada basis pengetahuan sistem.',
                    'duration' => 'Sesuai kondisi pengguna',
                    'body_location' => $this->bodyLocation($symptom['name']),
                    'frequency' => 'Tidak tentu',
                    'weight' => $this->symptomWeight($symptom['code']),
                    'is_active' => true,
                ]
            );
        }

        foreach ($data['rules'] as $rule) {
            Disease::updateOrCreate(
                ['code' => $rule['disease_code']],
                [
                    'name' => $rule['disease_name'],
                    'description' => 'Kemungkinan penyakit ringan berdasarkan kombinasi gejala '.implode(', ', $rule['symptoms']).'.',
                    'solution' => 'Gunakan obat sesuai aturan pakai umum, perhatikan alergi dan kondisi khusus. Jika keluhan tidak membaik dalam 3 x 24 jam, segera konsultasi ke dokter atau apoteker.',
                    'severity' => str_contains(strtolower($rule['disease_name']), 'berat') || str_contains(strtolower($rule['disease_name']), 'asma') ? 'Sedang' : 'Ringan',
                    'is_active' => true,
                ]
            );
        }

        foreach ($data['medicines'] as $medicine) {
            Medicine::updateOrCreate(
                ['code' => $medicine['code']],
                [
                    'name' => $medicine['name'],
                    'category' => $this->medicineCategory($medicine['name']),
                    'dosage' => 'Ikuti aturan pada kemasan atau arahan apoteker.',
                    'usage_rule' => 'Gunakan sesuai dosis umum dan jangan melebihi aturan pakai. Perhatikan batas swamedikasi maksimal 3 x 24 jam.',
                    'side_effects' => 'Efek samping dapat berbeda pada setiap orang. Hentikan penggunaan bila muncul reaksi alergi.',
                    'contraindication' => 'Hindari bila memiliki riwayat alergi terhadap kandungan obat. Ibu hamil, menyusui, anak, lansia, atau penderita penyakit kronis perlu berkonsultasi dahulu.',
                    'warning' => 'Sistem bersifat edukatif dan bukan pengganti diagnosis dokter.',
                    'description' => 'Data obat dari kodefikasi sistem pakar rekomendasi obat.',
                    'is_active' => true,
                ]
            );
        }

        foreach ($data['rules'] as $index => $rule) {
            $disease = Disease::where('code', $rule['disease_code'])->first();
            Rule::updateOrCreate(
                ['code' => $rule['code']],
                [
                    'disease_id' => $disease->id,
                    'symptom_codes' => $rule['symptoms'],
                    'medicine_codes' => $rule['medicines'],
                    'cf_value' => $this->ruleCf($index),
                    'method' => 'parallel',
                    'description' => 'IF '.implode(' AND ', $rule['symptoms']).' THEN '.$rule['disease_code'].' dengan output '.implode(', ', $rule['medicines']).'.',
                    'is_active' => true,
                ]
            );

            foreach ($rule['medicines'] as $medicineCode) {
                Medicine::where('code', $medicineCode)->update(['disease_id' => $disease->id]);
            }
        }
    }

    private function categoryForSymptom(string $code): string
    {
        $number = (int) substr($code, 1);
        return match (true) {
            $number <= 15 => 'Pernapasan dan demam',
            $number <= 25 => 'Pencernaan',
            $number <= 35 => 'Nyeri dan alergi',
            $number <= 50 => 'Kulit dan sistemik',
            $number <= 62 => 'Mata, telinga, mulut',
            $number <= 74 => 'Kulit dan otot',
            $number <= 86 => 'Metabolik dan urogenital',
            default => 'Lainnya',
        };
    }

    private function bodyLocation(string $name): string
    {
        $lower = strtolower($name);
        return match (true) {
            str_contains($lower, 'mata') => 'Mata',
            str_contains($lower, 'hidung') || str_contains($lower, 'batuk') || str_contains($lower, 'tenggorokan') => 'Pernapasan',
            str_contains($lower, 'perut') || str_contains($lower, 'mual') || str_contains($lower, 'diare') || str_contains($lower, 'bab') => 'Pencernaan',
            str_contains($lower, 'kulit') || str_contains($lower, 'gatal') || str_contains($lower, 'ruam') => 'Kulit',
            str_contains($lower, 'telinga') => 'Telinga',
            default => 'Umum',
        };
    }

    private function symptomWeight(string $code): float
    {
        $number = (int) substr($code, 1);
        return round(0.72 + (($number % 9) * 0.03), 2);
    }

    private function medicineCategory(string $name): string
    {
        $lower = strtolower($name);
        return match (true) {
            str_contains($lower, 'salep') || str_contains($lower, 'cream') || str_contains($lower, 'gel') || str_contains($lower, 'lotion') => 'Topikal',
            str_contains($lower, 'vitamin') || str_contains($lower, 'sangobion') || str_contains($lower, 'enervon') => 'Vitamin dan suplemen',
            str_contains($lower, 'tetes mata') || str_contains($lower, 'insto') || str_contains($lower, 'rohto') => 'Obat mata',
            str_contains($lower, 'batuk') || str_contains($lower, 'flu') || str_contains($lower, 'obh') => 'Batuk dan flu',
            str_contains($lower, 'antasida') || str_contains($lower, 'omeprazole') || str_contains($lower, 'polysilane') => 'Pencernaan',
            default => 'Obat umum',
        };
    }

    private function ruleCf(int $index): float
    {
        return round(0.82 + (($index % 6) * 0.02), 2);
    }
}
