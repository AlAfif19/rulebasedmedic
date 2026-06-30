@extends('layouts.app')
@section('title', 'Profil Pengguna')
@section('content')
<section class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
    <form method="POST" action="{{ route('profile.update') }}" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf @method('PUT')
        <h1 class="text-2xl font-semibold text-slate-950">Profil Pengguna</h1>
        <div class="mt-6 grid gap-4 md:grid-cols-2">
            <div><label class="form-label">Nama</label><input class="form-input" name="name" value="{{ old('name', auth()->user()->name) }}"></div>
            <div><label class="form-label">Username</label><input class="form-input" name="username" value="{{ old('username', auth()->user()->username) }}"></div>
            <div><label class="form-label">Email</label><input class="form-input" name="email" value="{{ old('email', auth()->user()->email) }}"></div>
            <div><label class="form-label">No HP</label><input class="form-input" name="phone" value="{{ old('phone', auth()->user()->phone) }}"></div>
            <div><label class="form-label">Password Baru</label><input class="form-input" type="password" name="password"></div>
            <div><label class="form-label">Konfirmasi Password</label><input class="form-input" type="password" name="password_confirmation"></div>
            <div class="md:col-span-2"><label class="form-label">Riwayat Alergi</label><textarea class="form-input" name="allergy_history">{{ old('allergy_history', auth()->user()->allergy_history) }}</textarea></div>
        </div>
        <button class="mt-5 rounded-2xl bg-cyan-700 px-5 py-3 text-sm font-semibold text-white">Simpan Profil</button>
    </form>
</section>
@endsection
