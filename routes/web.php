<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\GuestMenuController;
use App\Http\Controllers\VotingController;
use App\Http\Controllers\TransportasiController;
use App\Http\Controllers\BusController;

// ─── Landing ──────────────────────────────────────────────────────────────────
Route::get('/', [AuthController::class, 'landing'])->name('landing');

// ─── Auth — Admin ─────────────────────────────────────────────────────────────
Route::get('/login',        [AuthController::class, 'showLoginAdmin'])->name('login')->middleware('guest');
Route::post('/login',       [AuthController::class, 'loginAdmin'])->name('login.post');

// ─── Auth — Guest (NIK) ───────────────────────────────────────────────────────
Route::get('/login/guest',  [AuthController::class, 'showLoginGuest'])->name('login.guest')->middleware('guest');
Route::post('/login/guest', [AuthController::class, 'loginGuest'])->name('login.guest.post');

// ─── Logout (semua role) ──────────────────────────────────────────────────────
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ─── Admin Routes ─────────────────────────────────────────────────────────────
Route::middleware(['auth', \App\Http\Middleware\AdminOnly::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Karyawan
    Route::get('/karyawan/export', [KaryawanController::class, 'export'])->name('karyawan.export');
    Route::resource('karyawan', KaryawanController::class);

    // Import Excel
    Route::get('/import',           [ImportController::class, 'index'])->name('import.index');
    Route::post('/import/karyawan', [ImportController::class, 'importKaryawan'])->name('import.karyawan');
    Route::post('/import/detail',   [ImportController::class, 'importDetail'])->name('import.detail');

    // Suppliers
    Route::post('/suppliers/import', [SupplierController::class, 'import'])->name('suppliers.import');
    Route::resource('suppliers', SupplierController::class);

    // Guest Menu Management
    Route::get('/guest-menu',              [GuestMenuController::class, 'index'])->name('guest-menu.index');
    Route::post('/guest-menu/{id}/toggle', [GuestMenuController::class, 'toggle'])->name('guest-menu.toggle');
    Route::post('/guest-menu/reorder',     [GuestMenuController::class, 'reorder'])->name('guest-menu.reorder');

    // Voting kandidat tempat (CRUD) + reset
    Route::get('/voting',         [VotingController::class, 'index'])->name('voting.index');
    Route::post('/voting/reset',  [VotingController::class, 'resetVotes'])->name('voting.reset');
    Route::post('/voting',        [VotingController::class, 'store'])->name('voting.store');
    Route::put('/voting/{id}',    [VotingController::class, 'update'])->name('voting.update');
    Route::delete('/voting/{id}', [VotingController::class, 'destroy'])->name('voting.destroy');

    // Transportasi — lihat siapa naik bus & kendaraan pribadi
    Route::get('/buses',             [TransportasiController::class, 'buses'])->name('buses.index');
    Route::get('/buses/export',      [TransportasiController::class, 'exportBuses'])->name('buses.export');
    Route::get('/kendaraans',        [TransportasiController::class, 'kendaraans'])->name('kendaraans.index');
    Route::get('/kendaraans/export', [TransportasiController::class, 'exportKendaraans'])->name('kendaraans.export');
});

// ─── Guest Routes ─────────────────────────────────────────────────────────────
Route::middleware(['auth', \App\Http\Middleware\GuestOnly::class])->group(function () {
    Route::get('/my',                [GuestController::class, 'dashboard'])->name('guest.dashboard');
    Route::get('/my/menu/{key}',     [GuestController::class, 'menu'])->name('guest.menu');
    Route::post('/my/kehadiran',     [GuestController::class, 'konfirmasiKehadiran'])->name('guest.kehadiran');
    Route::get('/my/voting',         [VotingController::class, 'guestIndex'])->name('guest.voting');
    Route::post('/my/voting',        [VotingController::class, 'vote'])->name('guest.voting.post');

    // Transportasi — guest pilih bus atau kendaraan pribadi
    Route::post('/my/transportasi',        [BusController::class, 'store'])->name('guest.transportasi.store');
    Route::post('/my/transportasi/cancel', [BusController::class, 'cancel'])->name('guest.transportasi.cancel');
});