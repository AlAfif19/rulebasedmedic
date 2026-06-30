@extends('layouts.app')
@section('title', request('admin') ? 'Login Admin - DiagnoMed' : 'Login - DiagnoMed')
@section('content')
@php
    $heroPath = 'assets/images/medical-hero.svg';
    $heroVersion = file_exists(public_path($heroPath)) ? filemtime(public_path($heroPath)) : null;
    $heroSrc = asset($heroPath) . ($heroVersion ? '?v=' . $heroVersion : '');
@endphp
@if(request('admin'))
    <section class="-mt-20 grid min-h-screen place-items-center bg-gradient-to-br from-[#164775] via-[#1f5d95] to-[#2d91e6] px-4 pt-20">
        <form method="POST" action="{{ route('login.submit') }}" class="w-full max-w-md rounded-[8px] bg-white p-8 shadow-[0_24px_70px_rgba(0,0,0,0.22)]">
            @csrf
            <div class="mb-7 text-center">
                <x-diagnomed.logo class="justify-center" />
                <h1 class="mt-5 text-xl font-bold text-slate-950">Selamat Datang Admin</h1>
                <p class="mt-1 text-xs text-slate-500">Silahkan masukan untuk melanjutkan ke sistem</p>
            </div>
            <label class="form-label">Username atau Email</label>
            <input class="dm-input" name="login" value="{{ old('login') }}" placeholder="Masukan Username atau Email Anda" required autofocus>
            <label class="form-label mt-4">Password</label>
            <x-diagnomed.password-field name="password" placeholder="Masukan Password Anda" required="true" autocomplete="current-password" />
            <button class="dm-btn-primary mt-6 w-full" type="submit">Masuk</button>
        </form>
    </section>
@else
    <section class="dm-shell py-8 sm:py-12">
        <div class="mx-auto grid max-w-6xl overflow-hidden rounded-[6px] bg-[#f3f7fd] shadow-[0_20px_60px_rgba(31,93,149,0.08)] lg:grid-cols-[1fr_1.1fr]">
            <aside class="relative flex min-h-[520px] flex-col justify-between overflow-hidden bg-gradient-to-b from-[#2d91e6] to-[#164775] p-8 text-white sm:p-12">
                <div>
                    <h1 class="text-2xl font-bold">Selamat Datang Kembali</h1>
                    <p class="mt-2 text-sm text-blue-50">Masukan akun untuk melanjutkan ke sistem</p>
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

            <form method="POST" action="{{ route('login.submit') }}" class="flex min-h-[520px] flex-col justify-center px-6 py-10 sm:px-16">
                @csrf
                <div class="mb-7 text-center">
                    <x-diagnomed.logo class="justify-center" />
                </div>
                <label class="form-label">Username atau Email</label>
                <input class="dm-input" name="login" value="{{ old('login') }}" placeholder="Masukan Username atau Email Anda" required autofocus>
                <label class="form-label mt-4">Password</label>
                <x-diagnomed.password-field name="password" placeholder="Masukan Password Anda" required="true" autocomplete="current-password" />
                <div class="mt-4 flex items-center justify-between gap-3 text-xs">
                    <label class="flex items-center gap-2 font-medium text-slate-700">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-[#2385dd]">
                        Ingat Saya
                    </label>
                    <span class="font-semibold text-[#2385dd]">Lupa Password?</span>
                </div>
                <button class="dm-btn-primary mt-8 w-full" type="submit">Masuk</button>
                <p class="mt-6 text-center text-sm text-slate-600">Belum Punya Akun? <a class="font-semibold text-[#2385dd]" href="{{ route('register') }}">Daftar</a></p>
                <p class="mt-4 text-center text-xs text-slate-500">Admin apoteker masuk melalui <a class="font-semibold text-[#2385dd]" href="{{ route('login', ['admin' => 1]) }}">Login Admin</a></p>
            </form>
        </div>
    </section>
@endif
@endsection
