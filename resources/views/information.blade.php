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

    <section class="dm-card mt-8 overflow-hidden">
        <div class="grid gap-0 lg:grid-cols-[1fr_360px]">
            <div class="p-5 sm:p-6">
                <p class="text-xs font-bold uppercase tracking-[0.08em] text-[#2385dd]">Informasi Apotek</p>
                <h2 class="mt-2 text-2xl font-bold text-slate-950">{{ $contact['pharmacy_name'] }}</h2>
                <div class="mt-3 inline-flex items-center rounded-[4px] bg-green-100 px-2 py-1 text-xs font-bold text-green-700">Rating 5.0 dari 1 ulasan</div>
                <div class="mt-5 grid gap-3 text-sm leading-6 text-slate-700">
                    <p><span class="font-bold text-slate-950">Alamat:</span> {{ $contact['address'] }}</p>
                    <p><span class="font-bold text-slate-950">Jam buka:</span> {{ $contact['hours_short'] }}. Minggu tutup.</p>
                    <p><span class="font-bold text-slate-950">WhatsApp:</span> <a class="font-bold text-[#2385dd]" href="https://wa.me/{{ $contact['whatsapp'] }}">{{ $contact['phone'] }}</a></p>
                    <p><span class="font-bold text-slate-950">Plus Code:</span> {{ $contact['maps_plus_code'] }}</p>
                </div>
                <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ $contact['maps_url'] }}" target="_blank" rel="noopener" class="dm-btn-primary">Buka Google Maps</a>
                    <a href="https://wa.me/{{ $contact['whatsapp'] }}" target="_blank" rel="noopener" class="dm-btn-light text-[#2385dd]">Hubungi Apotek</a>
                </div>
                <p class="mt-4 break-all text-xs font-semibold text-slate-500">{{ $contact['maps_url'] }}</p>
            </div>
            <div class="min-h-72 border-t border-[#dce5f1] bg-[#f8fbff] lg:border-l lg:border-t-0">
                <iframe title="Lokasi Apotek Bhakti Medika Farma" class="h-full min-h-72 w-full" loading="lazy" src="{{ $contact['osm_embed_url'] }}"></iframe>
            </div>
        </div>
    </section>

    <div class="mt-8 dm-card overflow-hidden">
        <div class="flex flex-col justify-between gap-4 border-b border-[#dce5f1] bg-[#f8fbff] px-5 py-4 sm:flex-row sm:items-center">
            <div>
                <h2 class="text-lg font-bold text-slate-950">Daftar Obat Edukatif</h2>
                <p class="mt-1 text-xs text-slate-600">Informasi disusun untuk penyakit ringan dan bukan pengganti diagnosis medis.</p>
            </div>
            <form method="GET" action="{{ route('information') }}" data-live-search data-live-search-target="#medicine-results" class="flex h-10 items-center gap-2 rounded-full border border-slate-300 bg-white px-3">
                <x-diagnomed.icon name="search" class="h-4 w-4 text-slate-600" />
                <input name="q" value="{{ request('q') }}" type="search" autocomplete="off" class="h-full w-32 bg-transparent text-xs outline-none sm:w-44" placeholder="Cari obat, kategori, atau kode">
                @if(request('q'))
                    <a href="{{ route('information') }}" class="text-xs font-bold text-slate-400">Reset</a>
                @endif
            </form>
        </div>
        <div id="medicine-results" aria-live="polite">
            <div class="grid gap-4 p-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @forelse($medicines as $medicine)
                    <article class="rounded-[8px] border border-[#dce5f1] bg-white p-4">
                        <x-diagnomed.medicine-art :label="$medicine->name" :image="$medicine->image_path" class="mb-4" />
                        <div class="text-xs font-bold text-[#2385dd]">{{ $medicine->code }}</div>
                        <h3 class="mt-1 min-h-10 text-sm font-bold text-slate-950">{{ $medicine->name }}</h3>
                        <p class="mt-2 text-xs font-bold text-slate-900">Rp {{ number_format((int) $medicine->price, 0, ',', '.') }} {{ $medicine->price_unit ?: 'per strip' }}</p>
                        <div class="mt-2">
                            <x-diagnomed.badge :tone="$medicine->category">{{ $medicine->category ?: 'Obat umum' }}</x-diagnomed.badge>
                        </div>
                        <p class="mt-3 line-clamp-3 text-xs leading-5 text-slate-600">{{ $medicine->warning }}</p>
                    </article>
                @empty
                    <div class="rounded-[8px] border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-amber-800 sm:col-span-2 lg:col-span-3 xl:col-span-4">Obat tidak ditemukan. Coba kata kunci lain.</div>
                @endforelse
            </div>
            <div class="border-t border-[#dce5f1] px-5 py-4">
                <x-diagnomed.pagination :paginator="$medicines" />
            </div>
        </div>
    </div>
</section>
@endsection
