@props([
    'title' => 'Sistem Rekomendasi Obat',
    'subtitle' => 'Dapatkan rekomendasi obat yang tepat berdasarkan gejala yang Anda alami.',
    'compact' => false,
])

@php
    $heroPath = 'assets/images/medical-hero.svg';
    $heroVersion = file_exists(public_path($heroPath)) ? filemtime(public_path($heroPath)) : null;
    $heroSrc = asset($heroPath) . ($heroVersion ? '?v=' . $heroVersion : '');
@endphp

<section {{ $attributes->class(['dm-banner']) }}>
    <div class="grid items-center gap-4 px-6 py-5 sm:grid-cols-[1fr_auto] sm:px-8 {{ $compact ? 'sm:py-4' : 'sm:py-7' }}">
        <div>
            <h1 class="{{ $compact ? 'text-lg' : 'text-2xl' }} font-bold leading-tight text-white">{{ $title }}</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-blue-50">{{ $subtitle }}</p>
        </div>
        <div class="hidden sm:block">
            <img src="{{ $heroSrc }}" alt="" class="{{ $compact ? 'h-20' : 'h-32' }} w-auto">
        </div>
    </div>
</section>
