<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Disease;
use App\Models\Medicine;
use App\Models\Rule;
use App\Models\Symptom;
use App\Models\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    private const FORMATS = ['doc', 'pdf', 'excel', 'json', 'csv'];

    public function userHistoryPreview(Request $request)
    {
        return view('reports.user-history', $this->userHistoryData($request->user()));
    }

    public function userHistoryDownload(Request $request, string $format)
    {
        $data = $this->userHistoryData($request->user());

        return $this->download($data, $format, 'laporan-riwayat-konsultasi');
    }

    public function adminDashboardPreview()
    {
        return view('reports.admin-dashboard', $this->adminDashboardData());
    }

    public function adminDashboardDownload(string $format)
    {
        $data = $this->adminDashboardData();

        return $this->download($data, $format, 'laporan-semua-data-diagnomed');
    }

    private function download(array $data, string $format, string $baseFilename): Response
    {
        abort_unless(in_array($format, self::FORMATS, true), 404);

        $filename = $baseFilename.'-'.now()->format('Ymd-His');

        return match ($format) {
            'json' => response()
                ->json($this->jsonPayload($data))
                ->header('Content-Disposition', 'attachment; filename="'.$filename.'.json"'),
            'csv' => response($this->toCsv($data), 200, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="'.$filename.'.csv"',
            ]),
            'excel' => response($this->renderHtml($data, true), 200, [
                'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="'.$filename.'.xls"',
            ]),
            'doc' => response($this->renderHtml($data, true), 200, [
                'Content-Type' => 'application/msword; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="'.$filename.'.doc"',
            ]),
            'pdf' => response($this->toPdf($data), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$filename.'.pdf"',
            ]),
        };
    }

    private function userHistoryData(User $user): array
    {
        $consultations = Consultation::query()
            ->with(['disease', 'user'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return [
            'title' => 'Laporan Riwayat Konsultasi',
            'subtitle' => 'Riwayat diagnosa dan rekomendasi obat masyarakat.',
            'generated_at' => now(),
            'view' => 'reports.user-history',
            'owner' => $user,
            'summary' => [
                'total' => $consultations->count(),
                'diseases' => $consultations->whereNotNull('disease_id')->pluck('disease_id')->unique()->count(),
                'medicines' => $consultations->sum(fn (Consultation $item) => count(data_get($item->result_payload, 'medicines', []))),
                'latest' => optional($consultations->first())->created_at,
            ],
            'consultations' => $consultations,
            'sections' => [
                'Riwayat Konsultasi' => $this->consultationRows($consultations),
            ],
        ];
    }

    private function adminDashboardData(): array
    {
        $consultations = Consultation::query()->with(['user', 'disease'])->latest()->get();
        $symptoms = Symptom::query()->orderBy('code')->get();
        $diseases = Disease::query()->orderBy('code')->get();
        $medicines = Medicine::query()->with('disease')->orderBy('code')->get();
        $rules = Rule::query()->with('disease')->orderBy('code')->get();
        $users = User::query()->orderBy('role')->orderBy('name')->get();

        return [
            'title' => 'Laporan Semua Data Sistem',
            'subtitle' => 'Rekap data dashboard admin DiagnoMed.',
            'generated_at' => now(),
            'view' => 'reports.admin-dashboard',
            'counts' => [
                'Gejala' => $symptoms->count(),
                'Penyakit' => $diseases->count(),
                'Obat' => $medicines->count(),
                'Rule' => $rules->count(),
                'User' => $users->count(),
                'Riwayat' => $consultations->count(),
            ],
            'sections' => [
                'Data Gejala' => $symptoms->map(fn (Symptom $item) => [
                    'Kode' => $item->code,
                    'Nama' => $item->name,
                    'Kategori' => $item->category,
                    'Durasi' => $item->duration,
                    'Lokasi Tubuh' => $item->body_location,
                    'Frekuensi' => $item->frequency,
                    'Bobot' => $item->weight,
                    'Status' => $item->is_active ? 'Aktif' : 'Nonaktif',
                ])->all(),
                'Data Penyakit' => $diseases->map(fn (Disease $item) => [
                    'Kode' => $item->code,
                    'Nama' => $item->name,
                    'Keparahan' => $item->severity,
                    'Deskripsi' => $item->description,
                    'Solusi' => $item->solution,
                    'Status' => $item->is_active ? 'Aktif' : 'Nonaktif',
                ])->all(),
                'Data Obat' => $medicines->map(fn (Medicine $item) => [
                    'Kode' => $item->code,
                    'Nama' => $item->name,
                    'Penyakit' => optional($item->disease)->name,
                    'Kategori' => $item->category,
                    'Dosis' => $item->dosage,
                    'Aturan Pakai' => $item->usage_rule,
                    'Harga' => $item->price,
                    'Satuan Harga' => $item->price_unit,
                    'Status' => $item->is_active ? 'Aktif' : 'Nonaktif',
                ])->all(),
                'Data Rule' => $rules->map(fn (Rule $item) => [
                    'Kode' => $item->code,
                    'Penyakit' => optional($item->disease)->name,
                    'Gejala' => implode(', ', $item->symptom_codes ?? []),
                    'Obat' => implode(', ', $item->medicine_codes ?? []),
                    'CF' => $item->cf_value,
                    'Metode' => $item->method,
                    'Status' => $item->is_active ? 'Aktif' : 'Nonaktif',
                ])->all(),
                'Data User' => $users->map(fn (User $item) => [
                    'Nama' => $item->name,
                    'Username' => $item->username,
                    'Email' => $item->email,
                    'Role' => $item->role,
                    'Telepon' => $item->phone,
                    'Alamat' => $item->address,
                ])->all(),
                'Data Riwayat Konsultasi' => $this->consultationRows($consultations),
            ],
        ];
    }

    private function consultationRows(Collection $consultations): array
    {
        return $consultations->map(fn (Consultation $item) => [
            'Tanggal' => optional($item->created_at)->format('d M Y H:i'),
            'User' => optional($item->user)->name,
            'Penyakit' => optional($item->disease)->name ?? data_get($item->result_payload, 'disease.name', '-'),
            'Keparahan' => optional($item->disease)->severity ?? data_get($item->result_payload, 'disease.severity', '-'),
            'Metode' => strtoupper((string) $item->method),
            'Keyakinan' => number_format((float) $item->confidence_score, 0).'%',
            'Gejala' => implode(', ', $item->selected_symptom_codes ?? []),
            'Obat' => collect(data_get($item->result_payload, 'medicines', []))->pluck('name')->filter()->join(', '),
            'Catatan' => $item->notes,
            'Ringkasan' => $item->recommendation_summary,
        ])->all();
    }

    private function jsonPayload(array $data): array
    {
        return [
            'report' => [
                'title' => $data['title'],
                'subtitle' => $data['subtitle'],
                'generated_at' => $data['generated_at']->toIso8601String(),
            ],
            'summary' => $data['summary'] ?? $data['counts'] ?? [],
            'sections' => $data['sections'],
        ];
    }

    private function toCsv(array $data): string
    {
        $handle = fopen('php://temp', 'w+');
        fputcsv($handle, [$data['title']]);
        fputcsv($handle, ['Dibuat', $data['generated_at']->format('d M Y H:i').' WIB']);
        fputcsv($handle, []);

        foreach ($data['sections'] as $section => $rows) {
            fputcsv($handle, [$section]);
            $rows = collect($rows);

            if ($rows->isEmpty()) {
                fputcsv($handle, ['Tidak ada data']);
                fputcsv($handle, []);
                continue;
            }

            fputcsv($handle, array_keys($rows->first()));
            $rows->each(fn (array $row) => fputcsv($handle, array_map(fn ($value) => (string) $value, $row)));
            fputcsv($handle, []);
        }

        rewind($handle);
        return stream_get_contents($handle);
    }

    private function renderHtml(array $data, bool $document = false): string
    {
        $html = view($data['view'], $data + ['document' => $document])->render();

        return $document ? '<!doctype html><html><head><meta charset="utf-8"></head><body>'.$html.'</body></html>' : $html;
    }

    private function toPdf(array $data): string
    {
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', true);

        $pdf = new Dompdf($options);
        $pdf->loadHtml($this->renderHtml($data, true), 'UTF-8');
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        return $pdf->output();
    }
}
