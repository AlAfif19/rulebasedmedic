@extends('layouts.app')
@section('title', 'Registrasi - DiagnoMed')
@section('content')
@php
    $heroPath = 'assets/images/medical-hero.svg';
    $heroVersion = file_exists(public_path($heroPath)) ? filemtime(public_path($heroPath)) : null;
    $heroSrc = asset($heroPath) . ($heroVersion ? '?v=' . $heroVersion : '');
@endphp
<section class="dm-shell py-8 sm:py-12">
    <div class="mx-auto grid max-w-6xl overflow-hidden rounded-[6px] bg-[#f3f7fd] shadow-[0_20px_60px_rgba(31,93,149,0.08)] lg:grid-cols-[1fr_1.1fr]">
        <aside class="relative flex min-h-[640px] flex-col justify-between overflow-hidden bg-gradient-to-b from-[#2d91e6] to-[#164775] p-8 text-white sm:p-12">
            <div>
                <h1 class="text-2xl font-bold">Selamat Datang</h1>
                <p class="mt-2 text-sm text-blue-50">Buat akun untuk melanjutkan ke sistem</p>
            </div>
            <div class="mx-auto w-full max-w-sm">
                <img src="{{ $heroSrc }}" alt="" class="w-full">
            </div>
            <div>
                <h2 class="text-xl font-bold">Sistem Rekomendasi Obat</h2>
                <p class="mt-2 max-w-sm text-sm leading-6 text-blue-50">Dapatkan rekomendasi obat yang tepat berdasarkan gejala yang Anda alami dengan cepat dan akurat.</p>
                <div class="mt-8 flex gap-3">
                    <span class="h-3 w-3 rounded-full bg-white"></span>
                    <span class="h-3 w-3 rounded-full bg-white"></span>
                    <span class="h-3 w-3 rounded-full bg-white"></span>
                </div>
            </div>
        </aside>

        <form method="POST" action="{{ route('register.submit') }}" class="flex flex-col justify-center px-6 py-10 sm:px-16">
            @csrf
            <div class="mb-6 text-center">
                <x-diagnomed.logo class="justify-center" />
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="form-label">Nama Lengkap</label>
                    <input class="dm-input" name="name" value="{{ old('name') }}" placeholder="Masukan Nama Lengkap Anda" required>
                </div>
                <div>
                    <label class="form-label">Jenis Kelamin</label>
                    <select class="dm-input" name="gender">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="Laki-laki" @selected(old('gender') === 'Laki-laki')>Laki-laki</option>
                        <option value="Perempuan" @selected(old('gender') === 'Perempuan')>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Tanggal Lahir</label>
                    <input class="dm-input" type="date" name="birth_date" value="{{ old('birth_date') }}">
                </div>
                <div class="sm:col-span-2">
                    <label class="form-label">Username</label>
                    <input class="dm-input" name="username" value="{{ old('username') }}" placeholder="Buat Username Anda" required>
                </div>
                <div class="sm:col-span-2">
                    <label class="form-label">Email</label>
                    <input class="dm-input" type="email" name="email" value="{{ old('email') }}" placeholder="Masukan Email Anda" required>
                </div>
                <div class="sm:col-span-2">
                    <label class="form-label">No Handphone</label>
                    <input class="dm-input" name="phone" value="{{ old('phone') }}" placeholder="Masukan No Handphone Anda">
                </div>
                <div>
                    <label class="form-label">Password</label>
                    <x-diagnomed.password-field name="password" placeholder="Buat Password Anda" required="true" autocomplete="new-password" />
                </div>
                <div>
                    <label class="form-label">Konfirmasi Password</label>
                    <x-diagnomed.password-field name="password_confirmation" placeholder="Ulangi Password Anda" required="true" autocomplete="new-password" />
                </div>
                <div class="sm:col-span-2">
                    <label class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                        <input type="checkbox" name="pregnancy_status" value="1" class="rounded border-slate-300 text-[#2385dd]" @checked(old('pregnancy_status'))>
                        Sedang hamil atau menyusui
                    </label>
                </div>
            </div>
            <button class="dm-btn-primary mt-6 w-full" type="submit">Daftar</button>
            <p class="mt-6 text-center text-sm text-slate-600">Sudah Punya Akun? <a class="font-semibold text-[#2385dd]" href="{{ route('login') }}">Masuk</a></p>
        </form>
    </div>
</section>
@endsection
