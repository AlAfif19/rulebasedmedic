<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ResourceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'landing'])->name('landing');
Route::get('/informasi', [HomeController::class, 'information'])->name('information');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'role:masyarakat'])->group(function () {
    Route::get('/beranda', [HomeController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/cek-gejala', [ConsultationController::class, 'index'])->name('consultation.index');
    Route::post('/cek-gejala', [ConsultationController::class, 'diagnose'])->name('consultation.diagnose');
    Route::get('/riwayat', [ConsultationController::class, 'history'])->name('history.index');
    Route::get('/riwayat/{consultation}', [ConsultationController::class, 'show'])->name('consultation.show');
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::get('/{resource}', [ResourceController::class, 'index'])->name('resource.index');
    Route::get('/{resource}/create', [ResourceController::class, 'create'])->name('resource.create');
    Route::post('/{resource}', [ResourceController::class, 'store'])->name('resource.store');
    Route::get('/{resource}/{id}/edit', [ResourceController::class, 'edit'])->name('resource.edit');
    Route::put('/{resource}/{id}', [ResourceController::class, 'update'])->name('resource.update');
    Route::delete('/{resource}/{id}', [ResourceController::class, 'destroy'])->name('resource.destroy');
});
