@props(['compact' => false, 'light' => false])

<div {{ $attributes->class(['inline-flex items-center gap-2']) }}>
    <span class="relative grid h-9 w-9 place-items-center">
        <span class="absolute inset-1 rotate-45 rounded-[5px] border-2 {{ $light ? 'border-white/80' : 'border-[#2d91e6]' }}"></span>
        <span class="absolute h-6 w-2 rounded-full {{ $light ? 'bg-white' : 'bg-[#067a42]' }}"></span>
        <span class="absolute h-2 w-6 rounded-full {{ $light ? 'bg-white' : 'bg-[#067a42]' }}"></span>
    </span>
    <span class="leading-none">
        <span class="block text-sm font-bold {{ $light ? 'text-white' : 'text-[#2385dd]' }}">Diagno<span class="{{ $light ? 'text-white' : 'text-[#067a42]' }}">Med</span></span>
        @unless($compact)
            <span class="mt-1 block text-[9px] font-medium {{ $light ? 'text-blue-50' : 'text-slate-700' }}">Sistem Rekomendasi Obat</span>
        @endunless
    </span>
</div>
