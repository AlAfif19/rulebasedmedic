@extends('layouts.app')
@section('title', 'Informasi Obat - DiagnoMed')
@section('content')
<section class="dm-shell py-8 sm:py-10">
    <x-diagnomed.hero-banner
        title="Informasi Obat"
        subtitle="Pelajari aturan pakai, efek samping, peringatan penggunaan, dan batas aman swamedikasi sebelum memilih obat."
        compact="true"
    />

    <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['icon' => 'clipboard', 'title' => 'Aturan Pakai & Dosis', 'text' => 'Ikuti aturan pada kemasan atau arahan apoteker. Jangan menggandakan dosis saat lupa minum obat.'],
            ['icon' => 'info', 'title' => 'Efek Samping', 'text' => 'Hentikan penggunaan bila muncul ruam, sesak, bengkak, mual berat, atau reaksi tidak biasa.'],
            ['icon' => 'shield', 'title' => 'Kategori & Peringatan', 'text' => 'Perhatikan obat bebas, obat bebas terbatas, alergi, kehamilan, usia anak, lansia, dan penyakit kronis.'],
            ['icon' => 'clock', 'title' => 'Batas 3 x 24 Jam', 'text' => 'Jika keluhan tidak membaik atau memburuk dalam tiga hari, segera konsultasi ke dokter atau apoteker.'],
        ] as $info)
            <article class="dm-card magic-card p-5">
                <div class="grid h-11 w-11 place-items-center rounded-full bg-blue-100 text-[#2385dd]">
                    <x-diagnomed.icon :name="$info['icon']" class="h-5 w-5" />
                </div>
                <h2 class="mt-4 text-sm font-bold text-slate-950">{{ $info['title'] }}</h2>
                <p class="mt-2 text-xs leading-5 text-slate-600">{{ $info['text'] }}</p>
            </article>
        @endforeach
    </div>

    <div class="mt-8 dm-card overflow-hidden">
        <div class="flex flex-col justify-between gap-4 border-b border-[#dce5f1] bg-[#f8fbff] px-5 py-4 sm:flex-row sm:items-center">
            <div>
                <h2 class="text-lg font-bold text-slate-950">Daftar Obat Edukatif</h2>
                <p class="mt-1 text-xs text-slate-600">Informasi disusun untuk penyakit ringan dan bukan pengganti diagnosis medis.</p>
            </div>
            <div class="flex h-10 items-center gap-2 rounded-full border border-slate-300 bg-white px-3">
                <x-diagnomed.icon name="search" class="h-4 w-4 text-slate-600" />
                <span class="text-xs text-slate-500">Cari obat</span>
            </div>
        </div>
        <div class="grid gap-4 p-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($medicines as $medicine)
                <article class="rounded-[8px] border border-[#dce5f1] bg-white p-4">
                    <x-diagnomed.medicine-art :label="$medicine->name" class="mb-4" />
                    <div class="text-xs font-bold text-[#2385dd]">{{ $medicine->code }}</div>
                    <h3 class="mt-1 min-h-10 text-sm font-bold text-slate-950">{{ $medicine->name }}</h3>
                    <div class="mt-2">
                        <x-diagnomed.badge :tone="$medicine->category">{{ $medicine->category ?: 'Obat umum' }}</x-diagnomed.badge>
                    </div>
                    <p class="mt-3 line-clamp-3 text-xs leading-5 text-slate-600">{{ $medicine->warning }}</p>
                </article>
            @endforeach
        </div>
        <div class="border-t border-[#dce5f1] px-5 py-4">{{ $medicines->links() }}</div>
    </div>
</section>
@endsection
