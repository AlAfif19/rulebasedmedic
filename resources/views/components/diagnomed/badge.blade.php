@props(['tone' => 'default'])

@php
    $key = strtolower((string) $tone);
    $classes = match (true) {
        str_contains($key, 'demam') => 'bg-orange-100 text-orange-700',
        str_contains($key, 'pernapasan') => 'bg-emerald-100 text-emerald-700',
        str_contains($key, 'pencernaan') => 'bg-pink-100 text-pink-700',
        str_contains($key, 'kulit') => 'bg-purple-100 text-purple-700',
        str_contains($key, 'saraf') => 'bg-blue-100 text-blue-700',
        str_contains($key, 'tinggi') => 'bg-red-100 text-red-700',
        str_contains($key, 'sedang') => 'bg-amber-100 text-amber-700',
        str_contains($key, 'ringan') => 'bg-green-100 text-green-700',
        str_contains($key, 'matched') || str_contains($key, 'aktif') => 'bg-green-100 text-green-700',
        str_contains($key, 'unmatched') || str_contains($key, 'nonaktif') => 'bg-red-100 text-red-700',
        default => 'bg-slate-100 text-slate-700',
    };
@endphp

<span {{ $attributes->class(['inline-flex items-center rounded-[4px] px-2 py-1 text-[11px] font-bold', $classes]) }}>
    {{ $slot }}
</span>
