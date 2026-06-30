@extends('layouts.admin')
@section('title', 'Dashboard Admin')
@section('page_title', 'Dashboard')
@section('content')
@php
    $statIcons = ['Gejala' => 'stethoscope', 'Penyakit' => 'shield', 'Obat' => 'pill', 'Rule' => 'clipboard', 'User' => 'user', 'Riwayat' => 'history'];
    $maxDaily = max(1, $dailyConsultations->max('total'));
    $severityTotal = max(1, $severityDistribution->sum());
    $tinggi = (int) ($severityDistribution['Tinggi'] ?? 0);
    $sedang = (int) ($severityDistribution['Sedang'] ?? 0);
    $ringan = (int) ($severityDistribution['Ringan'] ?? 0);
    $tinggiDeg = round(($tinggi / $severityTotal) * 360);
    $sedangDeg = round(($sedang / $severityTotal) * 360);
@endphp

<div class="space-y-5">
    <x-diagnomed.hero-banner
        title="Selamat datang, Admin!"
        subtitle="Berikut adalah ringkasan informasi sistem rekomendasi obat."
        compact="true"
    />

    <div class="grid gap-4 md:grid-cols-3 xl:grid-cols-6">
        @foreach($counts as $label => $value)
            <article class="dm-card p-4">
                <div class="flex items-center gap-3">
                    <div class="grid h-11 w-11 place-items-center rounded-full bg-blue-100 text-[#2385dd]">
                        <x-diagnomed.icon :name="$statIcons[$label] ?? 'info'" class="h-5 w-5" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500">Total {{ $label }}</p>
                        <p class="text-xl font-bold text-slate-950">{{ $value }}</p>
                    </div>
                </div>
                <a href="#" class="mt-4 block text-xs font-bold text-[#2385dd]">Lihat Detail &rarr;</a>
            </article>
        @endforeach
    </div>

    <div class="grid gap-5 xl:grid-cols-[1.25fr_.75fr]">
        <section class="dm-card p-5">
            <h2 class="text-sm font-bold text-slate-950">Statistik Diagnosa <span class="font-medium text-slate-500">(7 Hari Terakhir)</span></h2>
            <div class="mt-5 h-56">
                <svg viewBox="0 0 700 220" class="h-full w-full" role="img" aria-label="Statistik diagnosa">
                    @foreach([40, 80, 120, 160, 200] as $line)
                        <line x1="40" y1="{{ $line }}" x2="680" y2="{{ $line }}" stroke="#dce5f1" stroke-width="1" />
                    @endforeach
                    @php
                        $points = $dailyConsultations->values()->map(function ($item, $index) use ($maxDaily) {
                            $x = 55 + ($index * 100);
                            $y = 190 - (($item['total'] / $maxDaily) * 150);
                            return ['x' => $x, 'y' => $y, 'total' => $item['total'], 'label' => $item['label']];
                        });
                    @endphp
                    <polyline fill="none" stroke="#2385dd" stroke-width="4" points="{{ $points->map(fn ($p) => $p['x'].','.$p['y'])->join(' ') }}" />
                    @foreach($points as $point)
                        <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="5" fill="#2385dd" />
                        <text x="{{ $point['x'] }}" y="{{ $point['y'] - 12 }}" text-anchor="middle" font-size="12" font-weight="700" fill="#1f2937">{{ $point['total'] }}</text>
                        <text x="{{ $point['x'] }}" y="214" text-anchor="middle" font-size="11" fill="#64748b">{{ $point['label'] }}</text>
                    @endforeach
                </svg>
            </div>
        </section>

        <section class="dm-card p-5">
            <h2 class="text-sm font-bold text-slate-950">Distribusi tingkat keparahan</h2>
            <div class="mt-5 grid gap-5 sm:grid-cols-[160px_1fr] sm:items-center">
                <div class="grid aspect-square place-items-center rounded-full" style="background: conic-gradient(#ef4444 0deg {{ $tinggiDeg }}deg, #f59e0b {{ $tinggiDeg }}deg {{ $tinggiDeg + $sedangDeg }}deg, #4ade80 {{ $tinggiDeg + $sedangDeg }}deg 360deg);">
                    <div class="grid h-24 w-24 place-items-center rounded-full bg-white text-center text-sm font-bold text-slate-950">
                        Total<br>{{ $severityTotal }}
                    </div>
                </div>
                <div class="grid gap-3 text-sm">
                    <div class="flex items-center justify-between"><span class="font-semibold text-red-600">Tinggi</span><span>{{ $tinggi }}</span></div>
                    <div class="flex items-center justify-between"><span class="font-semibold text-amber-600">Sedang</span><span>{{ $sedang }}</span></div>
                    <div class="flex items-center justify-between"><span class="font-semibold text-green-600">Ringan</span><span>{{ $ringan }}</span></div>
                </div>
            </div>
        </section>
    </div>

    <div class="grid gap-5 xl:grid-cols-[1.2fr_.8fr]">
        <section class="dm-card overflow-hidden">
            <div class="border-b border-[#dce5f1] bg-[#f8fbff] px-5 py-4">
                <h2 class="text-sm font-bold text-slate-950">Riwayat Diagnosa Terbaru</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="dm-table">
                    <thead>
                        <tr><th class="dm-th">No</th><th class="dm-th">Tanggal & Waktu</th><th class="dm-th">User</th><th class="dm-th">Penyakit</th><th class="dm-th">Keparahan</th><th class="dm-th">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @forelse($latest as $item)
                            <tr>
                                <td class="dm-td">{{ $loop->iteration }}</td>
                                <td class="dm-td">{{ $item->created_at->format('d M Y H:i') }}</td>
                                <td class="dm-td">{{ optional($item->user)->username }}</td>
                                <td class="dm-td font-semibold text-slate-900">{{ optional($item->disease)->name ?? '-' }}</td>
                                <td class="dm-td"><x-diagnomed.badge :tone="optional($item->disease)->severity">{{ optional($item->disease)->severity ?? '-' }}</x-diagnomed.badge></td>
                                <td class="dm-td"><x-diagnomed.icon name="search" class="text-[#2385dd]" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-8 text-center text-sm text-slate-500">Belum ada riwayat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="dm-card overflow-hidden">
            <div class="border-b border-[#dce5f1] bg-[#f8fbff] px-5 py-4">
                <h2 class="text-sm font-bold text-slate-950">Aktivitas Sistem Terbaru</h2>
            </div>
            <div class="grid gap-3 p-5">
                @foreach($activities as $activity)
                    <div class="flex items-center gap-3 rounded-[8px] border border-[#dce5f1] bg-white p-3">
                        <div class="grid h-9 w-9 place-items-center rounded-full bg-blue-100 text-[#2385dd]">
                            <x-diagnomed.icon name="info" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-xs font-bold text-slate-900">{{ $activity['text'] }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $activity['type'] }}</p>
                        </div>
                        <span class="text-xs font-semibold text-slate-500">{{ $activity['time'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</div>
@endsection
