@props(['active' => 1])

@php
    $steps = [
        1 => 'Pilih',
        2 => 'Konfirmasi',
        3 => 'Proses',
        4 => 'Hasil',
    ];
@endphp

<div {{ $attributes->class(['flex w-full items-center justify-center gap-2 sm:gap-4']) }}>
    @foreach($steps as $number => $label)
        <div class="flex items-center gap-2">
            <div class="grid h-9 w-9 place-items-center rounded-full text-sm font-bold {{ $number <= $active ? 'bg-[#2385dd] text-white' : 'bg-slate-200 text-slate-500' }}">
                {{ $number }}
            </div>
            <span class="hidden text-xs font-semibold {{ $number <= $active ? 'text-[#2385dd]' : 'text-slate-500' }} sm:inline">{{ $label }}</span>
        </div>
        @if(!$loop->last)
            <div class="h-px w-8 {{ $number < $active ? 'bg-[#2385dd]' : 'bg-slate-300' }} sm:w-16"></div>
        @endif
    @endforeach
</div>
