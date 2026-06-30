@extends('layouts.app')
@section('title', 'DiagnoMed - Sistem Rekomendasi Obat')
@section('content')
@php
    $heroPath = 'assets/images/medical-hero.svg';
    $heroVersion = file_exists(public_path($heroPath)) ? filemtime(public_path($heroPath)) : null;
    $heroSrc = asset($heroPath) . ($heroVersion ? '?v=' . $heroVersion : '');
@endphp
<section class="dm-shell py-8 sm:py-10">
    <div class="dm-banner motion-fade">
        <div class="grid items-center gap-6 px-6 py-8 md:grid-cols-[1fr_360px] md:px-10">
            <div>
                <h1 class="max-w-xl text-2xl font-bold leading-tight text-white sm:text-3xl">Sistem Rekomendasi Obat Berdasarkan Penyakit Ringan</h1>
                <p class="mt-3 max-w-xl text-sm leading-6 text-blue-50">Dapatkan rekomendasi obat yang tepat berdasarkan gejala yang Anda alami dengan cepat dan akurat.</p>
            </div>
            <img src="{{ $heroSrc }}" alt="" class="hidden w-full md:block">
        </div>
    </div>

    <div class="mt-5 flex flex-col gap-3 sm:flex-row">
        <a href="{{ auth()->check() ? route('consultation.index') : route('login') }}" class="dm-btn-primary w-full sm:w-auto">Mulai Cek Gejala</a>
        <a href="{{ auth()->check() ? route('history.index') : route('login') }}" class="dm-btn-green w-full sm:w-auto">Lihat Riwayat</a>
    </div>

    <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @foreach([
            ['icon' => 'search', 'title' => 'Diagnosa Akurat', 'text' => 'Sistem menganalisis gejala yang Anda alami.'],
            ['icon' => 'pill', 'title' => 'Rekomendasi Akurat', 'text' => 'Dapatkan rekomendasi obat yang sesuai dengan kondisi dan penyakit yang terdiagnosis.'],
            ['icon' => 'shield', 'title' => 'Aman & Terpercaya', 'text' => 'Informasi obat dilengkapi dosis, efek samping, dan kontraindikasi.'],
            ['icon' => 'clock', 'title' => 'Riwayat Tersimpan', 'text' => 'Semua riwayat diagnosa dan rekomendasi obat tersimpan aman.'],
        ] as $item)
            <article class="magic-card rounded-[8px] bg-transparent p-5">
                <div class="grid h-12 w-12 place-items-center rounded-full bg-blue-100 text-[#2385dd]">
                    <x-diagnomed.icon :name="$item['icon']" class="h-6 w-6" />
                </div>
                <h2 class="mt-4 text-sm font-bold text-slate-950">{{ $item['title'] }}</h2>
                <p class="mt-2 text-xs leading-5 text-slate-600">{{ $item['text'] }}</p>
            </article>
        @endforeach
    </div>

    <div class="mt-10 grid gap-6 lg:grid-cols-[260px_1fr] lg:items-start">
        <h2 class="text-2xl font-bold leading-tight text-slate-950">Bagaimana Cara Kerjanya ?</h2>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach([
                ['icon' => 'clipboard', 'title' => 'Pilih Gejala', 'text' => 'Pilih gejala yang Anda alami sesuai dengan kondisi Anda.'],
                ['icon' => 'clock', 'title' => 'Proses Analisis', 'text' => 'Sistem menganalisis gejala yang Anda deskripsikan.', 'active' => true],
                ['icon' => 'search', 'title' => 'Lihat Hasil', 'text' => 'Lihat hasil diagnosa penyakit berdasarkan gejala yang terpilih.'],
                ['icon' => 'pill', 'title' => 'Rekomendasi Obat', 'text' => 'Dapatkan rekomendasi obat yang sesuai dengan kondisi Anda.'],
            ] as $step)
                <article class="dm-card magic-card p-5 {{ $step['active'] ?? false ? 'bg-gradient-to-br from-[#1f5d95] to-[#2d91e6] text-white' : '' }}">
                    <x-diagnomed.icon :name="$step['icon']" class="h-8 w-8 {{ $step['active'] ?? false ? 'text-white' : 'text-slate-900' }}" />
                    <h3 class="mt-5 text-sm font-bold {{ $step['active'] ?? false ? 'text-white' : 'text-slate-950' }}">{{ $step['title'] }}</h3>
                    <p class="mt-2 min-h-12 text-xs leading-5 {{ $step['active'] ?? false ? 'text-blue-50' : 'text-slate-600' }}">{{ $step['text'] }}</p>
                    <div class="mt-5 text-right text-2xl leading-none">&rarr;</div>
                </article>
            @endforeach
        </div>
    </div>

    <div class="mt-8 flex items-start justify-center gap-2 text-center text-xs font-medium text-slate-700">
        <x-diagnomed.icon name="info" class="mt-0.5 h-4 w-4 shrink-0" />
        <p>Sistem hanya memberikan rekomendasi obat sebagai informasi. Untuk penanganan lebih lanjut, silahkan konsultasikan dengan dokter.</p>
    </div>
</section>
@endsection
