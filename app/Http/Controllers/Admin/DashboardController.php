<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Disease;
use App\Models\Medicine;
use App\Models\Rule;
use App\Models\Symptom;
use App\Models\User;
use Carbon\CarbonPeriod;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $latest = Consultation::query()->with(['user', 'disease'])->latest()->take(6)->get();
        $period = CarbonPeriod::create(now()->subDays(6)->startOfDay(), now()->startOfDay());
        $dailyRaw = Consultation::query()
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('day')
            ->pluck('total', 'day');

        $dailyConsultations = collect($period)->map(fn ($date) => [
            'label' => $date->format('d M'),
            'total' => (int) ($dailyRaw[$date->format('Y-m-d')] ?? 0),
        ]);

        $severityDistribution = Consultation::query()
            ->join('diseases', 'consultations.disease_id', '=', 'diseases.id')
            ->selectRaw('diseases.severity, COUNT(*) as total')
            ->groupBy('diseases.severity')
            ->pluck('total', 'severity');

        $activities = collect([
            ['type' => 'Obat', 'text' => 'Admin memperbarui data obat baru', 'time' => now()->subMinutes(25)->format('H:i')],
            ['type' => 'Penyakit', 'text' => 'Admin mengubah data penyakit', 'time' => now()->subMinutes(52)->format('H:i')],
            ['type' => 'Gejala', 'text' => 'Admin menambahkan data gejala baru', 'time' => now()->subHour()->format('H:i')],
            ['type' => 'User', 'text' => 'User baru mendaftar', 'time' => now()->subHours(2)->format('H:i')],
        ]);

        return view('admin.dashboard', [
            'counts' => [
                'Gejala' => Symptom::count(),
                'Penyakit' => Disease::count(),
                'Obat' => Medicine::count(),
                'Rule' => Rule::count(),
                'User' => User::where('role', 'masyarakat')->count(),
                'Riwayat' => Consultation::count(),
            ],
            'latest' => $latest,
            'dailyConsultations' => $dailyConsultations,
            'severityDistribution' => $severityDistribution,
            'activities' => $activities,
        ]);
    }
}
