@props(['label' => 'Obat'])

<div {{ $attributes->class(['relative grid aspect-[4/3] min-h-20 place-items-center overflow-hidden rounded-[6px] bg-gradient-to-br from-blue-50 to-white outline outline-1 outline-black/10']) }}>
    <div class="absolute -left-6 top-4 h-16 w-16 rounded-full bg-blue-200/60"></div>
    <div class="absolute -right-4 bottom-3 h-14 w-14 rounded-full bg-emerald-100"></div>
    <div class="relative h-16 w-28 rounded-[8px] border border-blue-200 bg-white shadow-[0_8px_22px_rgba(31,93,149,0.18)]">
        <div class="h-5 rounded-t-[8px] bg-[#2385dd]"></div>
        <div class="grid h-11 place-items-center px-2 text-center text-[10px] font-bold leading-tight text-[#1f5d95]">
            {{ Str::limit($label, 18) }}
        </div>
    </div>
</div>
