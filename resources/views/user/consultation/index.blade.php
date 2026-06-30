@extends('layouts.app')
@section('title', 'Cek Gejala - DiagnoMed')
@section('content')
@php
    $flatSymptoms = $symptoms->flatten();
    $categories = $symptoms->keys()->filter()->values();
@endphp
<section class="dm-shell py-8 sm:py-10">
    <x-diagnomed.hero-banner
        title="Cek Gejala"
        subtitle="Pilih semua gejala yang Anda rasakan. Semakin lengkap gejala dipilih, semakin akurat hasil diagnosa yang diberikan."
        compact="true"
    />

    <div class="mt-6 grid gap-5 lg:grid-cols-[1fr_430px] lg:items-start">
        <div class="flex items-center gap-4">
            <div class="grid h-16 w-16 place-items-center rounded-full bg-blue-100 text-[#2385dd]">
                <x-diagnomed.icon name="clipboard" class="h-8 w-8" />
            </div>
            <div>
                <p class="text-xs font-bold text-[#2385dd]">Langkah 1 dari 4</p>
                <h1 class="text-xl font-bold text-slate-950">Pilih Gejala</h1>
            </div>
        </div>
        <x-diagnomed.stepper :active="1" />
    </div>

    <form method="POST" action="{{ route('consultation.diagnose') }}" class="mt-5 grid gap-5 lg:grid-cols-[1fr_330px]">
        @csrf
        <div class="dm-card overflow-hidden">
            <div class="border-b border-[#dce5f1] p-4">
                <div class="flex h-11 items-center gap-2 rounded-[6px] border border-[#dce5f1] bg-white px-3">
                    <x-diagnomed.icon name="search" class="text-slate-500" />
                    <input data-filter-input data-live-filter-input type="search" autocomplete="off" enterkeyhint="search" class="h-full flex-1 bg-transparent text-sm outline-none" placeholder="Cari Gejala">
                </div>
                <div class="mt-4 flex gap-2 overflow-x-auto pb-1 text-xs font-bold" data-filter-categories>
                    <button type="button" data-filter-category="all" aria-pressed="true" class="inline-flex min-h-9 shrink-0 items-center gap-2 border-b-2 border-blue-500 px-2 text-blue-600 transition hover:text-[#2385dd]">
                        <span class="grid h-4 w-4 grid-cols-2 gap-0.5">
                            <span class="rounded-sm bg-[#2385dd]"></span><span class="rounded-sm bg-[#2385dd]"></span><span class="rounded-sm bg-[#2385dd]"></span><span class="rounded-sm bg-[#2385dd]"></span>
                        </span>
                        Semua Gejala
                    </button>
                    @foreach($categories as $category)
                        <button type="button" data-filter-category="{{ $category }}" aria-pressed="false" class="inline-flex min-h-9 shrink-0 items-center gap-2 border-b-2 border-transparent px-2 text-slate-800 transition hover:border-blue-200 hover:text-[#2385dd]">
                            <x-diagnomed.icon name="stethoscope" class="h-4 w-4" />
                            {{ $category }}
                        </button>
                    @endforeach
                </div>
            </div>
            <div class="grid gap-x-4 p-4 md:grid-cols-2">
                @foreach($flatSymptoms as $symptom)
                    <label data-filter-row data-symptom-category="{{ $symptom->category ?: 'Umum' }}" class="flex min-h-11 cursor-pointer items-center gap-3 rounded-[6px] px-2 text-xs hover:bg-blue-50">
                        <input data-symptom-checkbox data-symptom-label="{{ $symptom->name }}" type="checkbox" name="symptoms[]" value="{{ $symptom->code }}" class="rounded border-slate-300 text-[#2385dd]">
                        <span class="min-w-0 flex-1 truncate font-semibold text-slate-800">{{ $symptom->name }}</span>
                        <x-diagnomed.badge :tone="$symptom->category">{{ $symptom->category ?: 'Umum' }}</x-diagnomed.badge>
                    </label>
                @endforeach
            </div>
        </div>

        <aside class="dm-card h-max p-4 lg:sticky lg:top-28">
            <div class="flex items-center justify-between gap-3 border-b border-[#dce5f1] pb-3">
                <h2 class="text-sm font-bold text-slate-950">Gejala yang dipilih (<span data-selected-count>0</span>)</h2>
                <button type="reset" class="text-xs font-bold text-red-500">Bersihkan</button>
            </div>
            <p data-selected-empty class="mt-4 rounded-[6px] bg-slate-50 p-3 text-xs text-slate-500">Belum ada gejala dipilih.</p>
            <div data-selected-symptoms class="mt-4 grid gap-2"></div>

            <div class="mt-5 rounded-[8px] border border-blue-100 bg-blue-50 p-4">
                <p class="text-xs font-bold text-slate-950">Metode Analisis Paralel</p>
                <p class="mt-2 text-xs leading-5 text-slate-600">
                    Rule Based, Forward Chaining, Backward Chaining, dan Certainty Factor berjalan bersama untuk menghitung rekomendasi paling akurat.
                </p>
            </div>
            <label class="form-label mt-4">Catatan Tambahan</label>
            <textarea class="dm-textarea" rows="4" name="notes" placeholder="Contoh: sudah minum obat tertentu, alergi, atau durasi gejala"></textarea>

            <div class="mt-5 rounded-[8px] border border-[#dce5f1] bg-[#f8fbff] p-4">
                <h3 class="text-xs font-bold text-slate-950">Tips Pengisian</h3>
                <ul class="mt-3 grid gap-2 text-xs leading-5 text-slate-600">
                    <li>Pilih sesuai kondisi yang Anda alami saat ini.</li>
                    <li>Jangan memilih gejala yang tidak Anda rasakan.</li>
                    <li>Jika ragu, konsultasikan dengan tenaga medis.</li>
                </ul>
            </div>
            <button class="dm-btn-primary mt-5 w-full" type="submit">Selanjutnya</button>
        </aside>
    </form>
</section>
@endsection
