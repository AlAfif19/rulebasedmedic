@extends('layouts.app')
@section('title', 'Riwayat Penggunaan Obat - DiagnoMed')
@section('content')
<section class="dm-shell py-8 sm:py-10">
    <x-diagnomed.hero-banner
        title="Riwayat Penggunaan Obat"
        subtitle="Berikut adalah riwayat diagnosa dan rekomendasi obat yang pernah Anda lakukan."
        compact="true"
    />

    <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach([
            ['icon' => 'clipboard', 'label' => 'Total Diagnosis', 'value' => $summary['total'].' Total'],
            ['icon' => 'shield', 'label' => 'Penyakit Teridentifikasi', 'value' => $summary['diseases'].' Jenis'],
            ['icon' => 'pill', 'label' => 'Obat Direkomendasikan', 'value' => $summary['medicines'].' Jenis'],
            ['icon' => 'calendar', 'label' => 'Riwayat Terakhir', 'value' => $summary['latest'] ? $summary['latest']->format('d M Y') : '-'],
        ] as $card)
            <article class="dm-card flex items-center gap-3 p-4">
                <div class="grid h-11 w-11 place-items-center rounded-[6px] bg-blue-100 text-[#2385dd]">
                    <x-diagnomed.icon :name="$card['icon']" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-600">{{ $card['label'] }}</p>
                    <p class="mt-1 text-sm font-bold text-slate-950">{{ $card['value'] }}</p>
                </div>
            </article>
        @endforeach
    </div>

    <section class="dm-card mt-5 overflow-hidden">
        <form method="GET" action="{{ route('history.index') }}" data-live-search data-live-search-target="#history-results" class="grid gap-3 border-b border-[#dce5f1] bg-[#f8fbff] p-4 md:grid-cols-[1fr_1fr_1fr_1fr_auto]">
            <input class="dm-input" type="date" name="date_from" value="{{ request('date_from') }}">
            <input class="dm-input" type="date" name="date_to" value="{{ request('date_to') }}">
            <select class="dm-input" name="sort">
                <option value="latest" @selected(request('sort') !== 'oldest')>Terbaru</option>
                <option value="oldest" @selected(request('sort') === 'oldest')>Terlama</option>
            </select>
            <input class="dm-input" type="search" name="q" value="{{ request('q') }}" placeholder="Cari">
            <div class="flex gap-2">
                <button class="dm-btn-primary px-4" type="submit">Filter</button>
                <a href="{{ route('history.index') }}" class="dm-btn-light px-4">Reset</a>
            </div>
        </form>

        <div id="history-results" aria-live="polite">
            <div class="overflow-x-auto">
                <table class="dm-table">
                    <thead>
                        <tr>
                            <th class="dm-th">No</th>
                            <th class="dm-th">Tanggal & Waktu</th>
                            <th class="dm-th">Penyakit Teridentifikasi</th>
                            <th class="dm-th">Tingkat Keparahan</th>
                            <th class="dm-th">Jumlah Gejala</th>
                            <th class="dm-th">Obat Direkomendasikan</th>
                            <th class="dm-th">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($histories as $history)
                            <tr>
                                <td class="dm-td">{{ $histories->firstItem() + $loop->index }}</td>
                                <td class="dm-td">{{ $history->created_at->format('d M Y H:i') }} WIB</td>
                                <td class="dm-td font-semibold text-slate-900">{{ optional($history->disease)->name ?? '-' }}</td>
                                <td class="dm-td"><x-diagnomed.badge :tone="optional($history->disease)->severity">{{ optional($history->disease)->severity ?? '-' }}</x-diagnomed.badge></td>
                                <td class="dm-td">{{ count($history->selected_symptom_codes ?? []) }} Gejala</td>
                                <td class="dm-td">{{ count(data_get($history->result_payload, 'medicines', [])) }} Obat</td>
                                <td class="dm-td"><a class="font-bold text-[#2385dd]" href="{{ route('consultation.show', $history) }}">Lihat Detail &rarr;</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="p-8 text-center text-sm text-slate-500">Belum ada riwayat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-[#dce5f1] px-5 py-4">
                <x-diagnomed.pagination :paginator="$histories" />
            </div>
        </div>
    </section>

    @if($latestHistory)
        <section class="dm-card mt-5 overflow-hidden">
            <div class="flex items-center justify-between border-b border-[#dce5f1] bg-[#f8fbff] px-5 py-4">
                <h2 class="text-sm font-bold text-slate-950">Detail Riwayat Terakhir</h2>
                <a href="{{ route('consultation.show', $latestHistory) }}" class="text-xs font-bold text-[#2385dd]">Lihat Detail</a>
            </div>
            <div class="grid gap-5 p-5 lg:grid-cols-3">
                <div class="text-xs leading-6 text-slate-600">
                    <p><span class="font-bold text-slate-950">Tanggal & Waktu:</span><br>{{ $latestHistory->created_at->format('d M Y H:i') }} WIB</p>
                    <p class="mt-3"><span class="font-bold text-slate-950">Metode Analisis:</span><br>{{ strtoupper($latestHistory->method) }}</p>
                    <p class="mt-3"><span class="font-bold text-slate-950">Tingkat Ketepatan:</span><br>{{ number_format($latestHistory->confidence_score, 0) }}%</p>
                </div>
                <div class="text-xs leading-6 text-slate-600">
                    <p class="font-bold text-slate-950">{{ optional($latestHistory->disease)->name ?? 'Belum cocok' }}</p>
                    <p class="mt-2">{{ data_get($latestHistory->result_payload, 'disease.solution', 'Belum ada detail penyakit.') }}</p>
                </div>
                <div class="text-xs leading-6 text-slate-600">
                    <p class="font-bold text-slate-950">Rekomendasi Obat</p>
                    <ol class="mt-2 list-decimal space-y-1 pl-4">
                        @foreach(data_get($latestHistory->result_payload, 'medicines', []) as $medicine)
                            <li>{{ $medicine['name'] ?? '-' }}</li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </section>
    @endif
</section>
@endsection
