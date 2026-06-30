@extends('layouts.app')
@section('title', 'Hasil Diagnosis - DiagnoMed')
@section('content')
@php
    $payload = $consultation->result_payload ?? [];
    $medicines = collect(data_get($payload, 'medicines', []));
    $disease = data_get($payload, 'disease');
    $matched = data_get($payload, 'matched_rule', []);
    $confidence = (float) $consultation->confidence_score;
@endphp
<section class="dm-shell py-8 sm:py-10">
    <x-diagnomed.hero-banner
        title="Hasil Diagnosis"
        subtitle="Berikut adalah hasil diagnosa penyakit dan rekomendasi obat berdasarkan gejala yang Anda pilih."
        compact="true"
    />

    <div class="mt-6 grid gap-5 lg:grid-cols-[1fr_430px] lg:items-start">
        <div class="flex items-center gap-4">
            <div class="grid h-16 w-16 place-items-center rounded-full bg-blue-100 text-[#2385dd]">
                <x-diagnomed.icon name="clipboard" class="h-8 w-8" />
            </div>
            <div>
                <p class="text-xs font-bold text-[#2385dd]">Langkah 4 dari 4</p>
                <h1 class="text-xl font-bold text-slate-950">Hasil</h1>
            </div>
        </div>
        <x-diagnomed.stepper :active="4" />
    </div>

    <div class="mt-5 grid gap-5 lg:grid-cols-[1fr_1.1fr]">
        <div class="grid gap-5">
            <section class="dm-card p-5">
                <h2 class="text-sm font-bold text-slate-950">Gejala yang Anda Pilih ({{ $symptoms->count() }})</h2>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach($symptoms as $symptom)
                        <span class="inline-flex items-center rounded-[4px] bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700">{{ $symptom->name }}</span>
                    @endforeach
                </div>
            </section>

            <section class="dm-card p-5">
                <h2 class="text-sm font-bold text-slate-950">Hasil Diagnosa Penyakit</h2>
                <article class="mt-4 rounded-[8px] border border-[#dce5f1] bg-white p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <x-diagnomed.badge :tone="data_get($disease, 'severity', 'unmatched')">{{ $confidence < 50 ? 'Kemungkinan awal' : data_get($disease, 'severity', 'Belum cocok') }}</x-diagnomed.badge>
                            <h3 class="mt-3 text-base font-bold text-slate-950">{{ data_get($disease, 'name', 'Belum ada kecocokan penuh') }}</h3>
                            <p class="mt-1 text-xs leading-5 text-slate-600">Tingkat Keparahan dan Ketepatan</p>
                            <p class="mt-3 text-xs leading-5 text-slate-600">{{ data_get($disease, 'solution', 'Pilih gejala yang lebih spesifik atau hubungi apoteker untuk arahan lebih aman.') }}</p>
                            @if($confidence < 50)
                                <p class="mt-3 rounded-[6px] bg-amber-50 px-3 py-2 text-xs font-semibold leading-5 text-amber-800">Skor rendah karena gejala yang dipilih hanya cocok sebagian dengan rule. Tambahkan gejala yang benar-benar Anda alami untuk hasil yang lebih kuat.</p>
                            @endif
                        </div>
                        <div class="grid h-16 w-16 shrink-0 place-items-center rounded-full border-4 border-[#2385dd] bg-blue-50 text-sm font-bold text-[#2385dd]">
                            {{ number_format($consultation->confidence_score, 0) }}%
                        </div>
                    </div>
                    @if($matched)
                        <div class="mt-4 grid gap-2 rounded-[6px] bg-[#f8fbff] p-3 text-xs text-slate-600">
                            <div>Rule: <span class="font-bold text-slate-900">{{ data_get($matched, 'code', '-') }}</span></div>
                            <div>Kecocokan: <span class="font-bold text-slate-900">{{ data_get($matched, 'match_ratio', 0) }}%</span></div>
                            @if(data_get($matched, 'matched_symptoms'))
                                <div>Gejala cocok: <span class="font-bold text-slate-900">{{ implode(', ', data_get($matched, 'matched_symptoms', [])) }}</span></div>
                            @endif
                            @if(data_get($matched, 'missing_symptoms'))
                                <div>Gejala rule yang belum dipilih: <span class="font-bold text-slate-900">{{ implode(', ', data_get($matched, 'missing_symptoms', [])) }}</span></div>
                            @endif
                        </div>
                    @endif
                </article>
            </section>
        </div>

        <section class="dm-card overflow-hidden">
            <div class="border-b border-[#dce5f1] bg-[#f8fbff] px-5 py-4">
                <h2 class="text-sm font-bold text-slate-950">Rekomendasi Obat</h2>
            </div>
            <div class="grid gap-3 p-5">
                @forelse($medicines as $medicine)
                    <article class="grid gap-4 rounded-[8px] border border-[#dce5f1] bg-white p-3 sm:grid-cols-[86px_1fr_auto] sm:items-center">
                        <x-diagnomed.medicine-art :label="$medicine['name'] ?? 'Obat'" :image="$medicine['image_path'] ?? null" class="h-20" />
                        <div>
                            <p class="text-xs font-semibold text-slate-600">{{ $medicine['dosage'] ?? 'Ikuti aturan pakai' }}</p>
                            <h3 class="mt-1 text-sm font-bold text-slate-950">{{ $medicine['name'] ?? '-' }}</h3>
                            <p class="mt-1 line-clamp-2 text-xs leading-5 text-slate-600">{{ $medicine['usage_rule'] ?? 'Gunakan sesuai aturan pakai pada kemasan.' }}</p>
                            <button type="button" data-modal-open="#medicine-modal-{{ $loop->index }}" class="mt-2 text-xs font-bold text-[#2385dd]">Detail Obat &rarr;</button>
                        </div>
                        <div class="rounded-[6px] border border-[#dce5f1] bg-[#f8fbff] px-3 py-2 text-center text-xs font-bold text-slate-800">3 Kali Sehari<br>Setelah Makan</div>
                    </article>

                    <div id="medicine-modal-{{ $loop->index }}" data-modal class="fixed inset-0 z-50 hidden bg-slate-950/45 p-4">
                        <div class="mx-auto mt-10 w-full max-w-4xl overflow-hidden rounded-[8px] bg-white shadow-[0_24px_70px_rgba(0,0,0,0.24)]">
                            <div class="flex items-center justify-between border-b border-[#dce5f1] px-5 py-4">
                                <h3 class="text-sm font-bold text-slate-950">Obat Lainnya</h3>
                                <button type="button" data-modal-close class="grid h-10 w-10 place-items-center rounded-full hover:bg-slate-100" aria-label="Tutup">x</button>
                            </div>
                            <div class="grid gap-5 bg-[#f2f6fc] p-5 md:grid-cols-[280px_1fr]">
                                <x-diagnomed.medicine-art :label="$medicine['name'] ?? 'Obat'" :image="$medicine['image_path'] ?? null" class="min-h-[196px]" />
                                <div class="rounded-[8px] bg-white p-5">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h4 class="text-xl font-bold text-slate-950">{{ $medicine['name'] ?? '-' }}</h4>
                                            <p class="mt-1 text-sm text-slate-600">{{ $medicine['dosage'] ?? '-' }}</p>
                                        </div>
                                        <p class="text-xs font-bold text-slate-900">Rp. 5000/Strip</p>
                                    </div>
                                    <div class="mt-4 grid gap-4 text-xs leading-5 text-slate-700">
                                        <div>
                                            <h5 class="text-sm font-bold text-slate-950">Informasi Obat</h5>
                                            <p class="mt-2">{{ $medicine['description'] ?? 'Informasi belum tersedia.' }}</p>
                                        </div>
                                        <div class="grid gap-3 border-y border-[#dce5f1] py-4 sm:grid-cols-[96px_1fr]">
                                            <span class="font-bold text-slate-950">Bentuk</span>
                                            <span>{{ $medicine['category'] ?? 'Tablet' }}</span>
                                            <span class="font-bold text-slate-950">Produsen</span>
                                            <span>Contoh: Generik Berlogo, Kimia Farma</span>
                                        </div>
                                        <div><span class="font-bold text-slate-950">Aturan pakai:</span> {{ $medicine['usage_rule'] ?? 'Informasi belum tersedia.' }}</div>
                                        <div><span class="font-bold text-slate-950">Efek samping:</span> {{ $medicine['side_effects'] ?? 'Informasi belum tersedia.' }}</div>
                                        <div><span class="font-bold text-slate-950">Kontraindikasi:</span> {{ $medicine['contraindication'] ?? 'Informasi belum tersedia.' }}</div>
                                        <div><span class="font-bold text-slate-950">Peringatan:</span> {{ $medicine['warning'] ?? 'Informasi belum tersedia.' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-[8px] border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-amber-800">Tidak ada rekomendasi obat otomatis. Silahkan konsultasi ke apoteker.</div>
                @endforelse
            </div>
            <div class="border-t border-[#dce5f1] bg-[#f8fbff] p-5">
                <h3 class="text-sm font-bold text-slate-950">Saran & Anjuran :</h3>
                <ul class="mt-3 grid gap-2 text-xs leading-5 text-slate-700">
                    <li>Perbanyak istirahat dan konsumsi air putih.</li>
                    <li>Hindari makanan yang dapat memperburuk kondisi seperti makanan berminyak atau bersoda.</li>
                    <li>Segera konsultasikan ke dokter jika dalam 3 hari atau muncul tanda bahaya seperti sesak, muntah darah, atau nyeri berat.</li>
                </ul>
            </div>
        </section>
    </div>

    <div class="mt-5 flex items-start justify-center gap-2 text-center text-xs font-semibold text-slate-700">
        <x-diagnomed.icon name="info" class="mt-0.5 h-4 w-4 shrink-0" />
        <p>Hasil ini bukanlah diagnosis medis. Untuk penanganan lebih lanjut, silahkan konsultasikan dengan dokter.</p>
    </div>
</section>
@endsection
