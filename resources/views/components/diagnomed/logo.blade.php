@props(['compact' => false, 'light' => false])

@php
    $logoPath = 'assets/images/logo.svg';
    $logoVersion = file_exists(public_path($logoPath)) ? filemtime(public_path($logoPath)) : null;
    $logoSrc = asset($logoPath) . ($logoVersion ? '?v=' . $logoVersion : '');
@endphp

<div {{ $attributes->class(['inline-flex items-center gap-2']) }}>
    <img
        src="{{ $logoSrc }}"
        alt="DiagnoMed"
        class="{{ $compact ? 'h-10 w-10' : 'h-11 w-auto max-w-[168px]' }} shrink-0 object-contain"
    >
    @unless($compact)
        <span class="leading-none">
            <span class="block text-sm font-bold {{ $light ? 'text-white' : 'text-[#2385dd]' }}">Diagno<span class="{{ $light ? 'text-white' : 'text-[#067a42]' }}">Med</span></span>
            <span class="mt-1 block text-[9px] font-medium {{ $light ? 'text-blue-50' : 'text-slate-700' }}">Sistem Rekomendasi Obat</span>
        </span>
    @endunless
</div>
