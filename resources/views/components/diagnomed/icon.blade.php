@props(['name' => 'info'])

@php
    $paths = [
        'home' => '<path d="m3 10 9-7 9 7"/><path d="M5 10v10h14V10"/><path d="M9 20v-6h6v6"/>',
        'stethoscope' => '<path d="M6 4v5a4 4 0 0 0 8 0V4"/><path d="M4 4h4"/><path d="M12 4h4"/><path d="M14 9a5 5 0 0 0 5 5v1a4 4 0 0 1-8 0"/>',
        'history' => '<path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/><path d="M12 7v5l3 2"/>',
        'info' => '<circle cx="12" cy="12" r="9"/><path d="M12 10v6"/><path d="M12 7h.01"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/>',
        'bell' => '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 7h18s-3 0-3-7"/><path d="M10 19a2 2 0 0 0 4 0"/>',
        'calendar' => '<rect x="4" y="5" width="16" height="15" rx="2"/><path d="M16 3v4"/><path d="M8 3v4"/><path d="M4 10h16"/>',
        'edit' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
        'trash' => '<path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v5"/><path d="M14 11v5"/>',
        'logout' => '<path d="M10 17l5-5-5-5"/><path d="M15 12H3"/><path d="M21 19V5a2 2 0 0 0-2-2h-6"/>',
        'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
        'pill' => '<path d="M10 21 3 14a5 5 0 0 1 7-7l7 7a5 5 0 0 1-7 7Z"/><path d="m8.5 8.5 7 7"/>',
        'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-5"/>',
        'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/>',
        'clipboard' => '<path d="M9 4h6l1 2h3v15H5V6h3Z"/><path d="M9 4a3 3 0 0 1 6 0"/><path d="M8 12h8"/><path d="M8 16h5"/>',
        'plus' => '<path d="M12 5v14"/><path d="M5 12h14"/>',
        'filter' => '<path d="M4 5h16l-6 7v5l-4 2v-7Z"/>',
        'reset' => '<path d="M4 4v6h6"/><path d="M20 20v-6h-6"/><path d="M20 9a8 8 0 0 0-14-3L4 10"/><path d="M4 15a8 8 0 0 0 14 3l2-4"/>',
    ];
@endphp

<svg {{ $attributes->class(['h-4 w-4']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    {!! $paths[$name] ?? $paths['info'] !!}
</svg>
