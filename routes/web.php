<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SpreedsheetController;
use App\Http\Controllers\RealisasiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HarmetController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Middleware\RoleMiddleware;

// Redirect root ke halaman login
Route::get('/', fn () => redirect()->route('login'));

// ==============================
// ROUTE UNTUK ADMIN SAJA
// ==============================
Route::middleware([
    'auth',
    'verified',
    RoleMiddleware::class . ':admin',
])->group(function () {
    // Halaman dashboard admin
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Manajemen pengguna
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::post('/user/{id}/reset-password', [UserController::class, 'resetPassword'])->name('user.resetPassword');

    // Manajemen profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==============================
// ROUTE UNTUK ADMIN & USER (AKSES UMUM)
// ==============================
Route::middleware(['auth', 'verified'])->group(function () {
    // Halaman dashboard user biasa
    Route::get('user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');

    // Halaman Harmet
    Route::get('/harmet', [HarmetController::class, 'index'])->name('harmet.index');

    // Halaman Billing
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');

    // Data P2TL & Realisasi
    Route::prefix('data')->name('data.')->group(function () {
        Route::get('/', fn () => redirect()->route('data.p2tl'));
        Route::get('/p2tl', [SpreedsheetController::class, 'data'])->name('p2tl');
        Route::get('/realisasi', [RealisasiController::class, 'index'])->name('realisasi');
        Route::get('/realisasi/idpel/{idpel}', [RealisasiController::class, 'byIdpel'])->name('realisasi.byIdpel');
        Route::get('/data/realisasi/{idpel}', [SpreedsheetController::class, 'realisasiByIdpel'])->name('data.realisasi.byIdpel');
        Route::get('p2tl/{id}/edit', [SpreedsheetController::class, 'edit'])->name('p2tl.edit');
        Route::post('p2tl/{id}/update', [SpreedsheetController::class, 'update'])->name('p2tl.update');

    });
});

// ==============================
// AUTH ROUTES (DARI LARAVEL BREEZE)
// ==============================
require __DIR__ . '/auth.php';
