@extends('layouts.app')
@section('title', 'Beranda - DiagnoMed')
@section('content')
<section class="dm-shell py-8 sm:py-10">
    <x-diagnomed.hero-banner
        title="Sistem Rekomendasi Obat Berdasarkan Penyakit Ringan"
        subtitle="Selamat datang, {{ auth()->user()->name }}. Mulai pilih gejala untuk mendapatkan rekomendasi obat edukatif."
    />
    <div class="mt-5 flex flex-col gap-3 sm:flex-row">
        <a href="{{ route('consultation.index') }}" class="dm-btn-primary">Mulai Cek Gejala</a>
        <a href="{{ route('history.index') }}" class="dm-btn-green">Lihat Riwayat</a>
    </div>
    <div class="mt-8 grid gap-5 md:grid-cols-3">
        <div class="dm-card p-5">
            <div class="text-xs font-bold text-[#2385dd]">Total Gejala</div>
            <div class="mt-2 text-3xl font-bold text-slate-950">{{ $symptomCount }}</div>
            <p class="mt-2 text-xs text-slate-600">Gejala tersedia untuk proses analisis.</p>
        </div>
        <div class="dm-card p-5">
            <div class="text-xs font-bold text-[#2385dd]">Total Obat</div>
            <div class="mt-2 text-3xl font-bold text-slate-950">{{ $medicineCount }}</div>
            <p class="mt-2 text-xs text-slate-600">Obat edukatif dalam basis pengetahuan.</p>
        </div>
        <div class="dm-card p-5">
            <div class="text-xs font-bold text-[#2385dd]">Batas Aman</div>
            <div class="mt-2 text-3xl font-bold text-slate-950">3 x 24</div>
            <p class="mt-2 text-xs text-slate-600">Jam batas swamedikasi sebelum konsultasi.</p>
        </div>
    </div>

    <div class="mt-8 dm-card overflow-hidden">
        <div class="border-b border-[#dce5f1] bg-[#f8fbff] px-5 py-4">
            <h2 class="text-lg font-bold text-slate-950">Penyakit Ringan yang Tersedia</h2>
        </div>
        <div class="grid gap-4 p-5 md:grid-cols-3">
            @foreach($featuredDiseases as $disease)
                <article class="rounded-[8px] border border-[#dce5f1] bg-white p-4">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-xs font-bold text-[#2385dd]">{{ $disease->code }}</span>
                        <x-diagnomed.badge :tone="$disease->severity">{{ $disease->severity }}</x-diagnomed.badge>
                    </div>
                    <h3 class="mt-3 text-sm font-bold text-slate-950">{{ $disease->name }}</h3>
                    <p class="mt-2 line-clamp-3 text-xs leading-5 text-slate-600">{{ $disease->solution }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endsection
