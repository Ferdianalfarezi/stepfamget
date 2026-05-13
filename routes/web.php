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
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\KonveksiController;
use App\Http\Controllers\PenerimaanBajuController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KetuaBusController;

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

    Route::resource('users', UserController::class)->except(['create', 'show']);

    // Karyawan — route eksplisit HARUS di atas resource supaya tidak bentrok
    Route::get('/karyawan/export',        [KaryawanController::class, 'export'])->name('karyawan.export');
    Route::post('/karyawan/import-baju',  [KaryawanController::class, 'importBaju'])->name('karyawan.importBaju');
    Route::resource('karyawan', KaryawanController::class);

    // Import Excel (halaman terpisah)
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

    // Transportasi
    Route::get('/buses',             [TransportasiController::class, 'buses'])->name('buses.index');
    Route::get('/buses/export',      [TransportasiController::class, 'exportBuses'])->name('buses.export');
    Route::get('/kendaraans',        [TransportasiController::class, 'kendaraans'])->name('kendaraans.index');
    Route::get('/kendaraans/export', [TransportasiController::class, 'exportKendaraans'])->name('kendaraans.export');
    Route::post('buses/import-kursi', [TransportasiController::class, 'importKursi'])->name('buses.importKursi');

    // Pengajuan Anggota Keluarga
    Route::get('/pengajuan',               [PengajuanController::class, 'index'])->name('pengajuan.index');
    Route::post('/pengajuan/{id}/approve', [PengajuanController::class, 'approve'])->name('pengajuan.approve');
    Route::post('/pengajuan/{id}/reject',  [PengajuanController::class, 'reject'])->name('pengajuan.reject');

    // Konveksi
    Route::get('/konveksis',              [KonveksiController::class, 'index'])->name('konveksi.index');
    Route::get('/konveksis/export',       [KonveksiController::class, 'export'])->name('konveksi.export');
    Route::post('/konveksis/scan',        [KonveksiController::class, 'scan'])->name('konveksi.scan');
    Route::post('/konveksis/reset-scan',  [KonveksiController::class, 'resetScan'])->name('konveksi.resetScan');
    Route::get('/konveksis/print',        [KonveksiController::class, 'print'])->name('konveksi.print');

    Route::get('/penerimaan-baju',                   [PenerimaanBajuController::class, 'index'])->name('penerimaan-baju.index');
    Route::get('/penerimaan-baju/export',            [PenerimaanBajuController::class, 'export'])->name('penerimaan-baju.export');
    Route::get('/penerimaan-baju/print',             [PenerimaanBajuController::class, 'print'])->name('penerimaan-baju.print');
    Route::post('/penerimaan-baju/scan',             [PenerimaanBajuController::class, 'scan'])->name('penerimaan-baju.scan');
    Route::post('/penerimaan-baju/scan-departemen',  [PenerimaanBajuController::class, 'scanDepartemen'])->name('penerimaan-baju.scanDepartemen');
    Route::post('/penerimaan-baju/reset-nik',        [PenerimaanBajuController::class, 'resetNik'])->name('penerimaan-baju.resetNik');
    Route::post('/penerimaan-baju/reset-scan',       [PenerimaanBajuController::class, 'resetScan'])->name('penerimaan-baju.resetScan');

    Route::get('/bus/card',          [KetuaBusController::class, 'card'])->name('bus.card');
    Route::get('/bus/layout/{kode}', [KetuaBusController::class, 'layout'])->name('bus.layout');
    Route::get('/bus/ketua',         [KetuaBusController::class, 'index'])->name('bus.ketua.index');
    Route::post('/bus/ketua',        [KetuaBusController::class, 'store'])->name('bus.ketua.store');
    Route::get('/bus/ketua/{id}/edit', [KetuaBusController::class, 'edit'])->name('bus.ketua.edit');
    Route::put('/bus/ketua/{id}',    [KetuaBusController::class, 'update'])->name('bus.ketua.update');
    Route::delete('/bus/ketua/{id}', [KetuaBusController::class, 'destroy'])->name('bus.ketua.destroy');
    
});

// ─── Guest Routes ─────────────────────────────────────────────────────────────
Route::middleware(['auth', \App\Http\Middleware\GuestOnly::class])->group(function () {
    Route::get('/my',                [GuestController::class, 'dashboard'])->name('guest.dashboard');
    Route::get('/my/menu/{key}',     [GuestController::class, 'menu'])->name('guest.menu');
    Route::post('/my/kehadiran',     [GuestController::class, 'konfirmasiKehadiran'])->name('guest.kehadiran');
    Route::get('/my/voting',         [VotingController::class, 'guestIndex'])->name('guest.voting');
    Route::post('/my/voting',        [VotingController::class, 'vote'])->name('guest.voting.post');

    // Transportasi
    Route::post('/my/transportasi',        [BusController::class, 'store'])->name('guest.transportasi.store');
    Route::post('/my/transportasi/cancel', [BusController::class, 'cancel'])->name('guest.transportasi.cancel');

    // Pengajuan Anggota Keluarga
    Route::post('/my/pengajuan', [PengajuanController::class, 'store'])->name('guest.pengajuan.store');

    Route::get('/my/baju',         [GuestController::class, 'baju'])->name('guest.baju.index');
    Route::post('/my/baju/update', [GuestController::class, 'bajuUpdate'])->name('guest.baju.update');
});