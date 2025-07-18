<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SpreedsheetController;
use App\Http\Controllers\RealisasiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HarmetController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\UserController;

// Redirect root ke halaman login
Route::get('/', fn () => redirect()->route('login'));

// Route yang hanya bisa diakses jika sudah login dan verifikasi
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::get('/harmet', [HarmetController::class, 'index'])->name('harmet.index');
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    Route::post('/user/{id}/reset-password', [App\Http\Controllers\UserController::class, 'resetPassword'])
    ->name('user.resetPassword')
    ->middleware('auth');

    // Group data P2TL & Realisasi
    Route::prefix('data')->name('data.')->group(function () {
        Route::get('/', fn () => redirect()->route('data.p2tl')); // ✅ FIXED redirect
        Route::get('/p2tl', [SpreedsheetController::class, 'data'])->name('p2tl');
        Route::get('/realisasi', [RealisasiController::class, 'index'])->name('realisasi');
    });

    // Manajemen profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Auth route bawaan Laravel Breeze/Fortify
require __DIR__.'/auth.php';
