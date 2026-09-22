<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\PetaController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\VerifikasiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriHambatanController;
use App\Http\Controllers\FasilitasPublikController;
use App\Http\Controllers\PengaturanPrioritasController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\KonfigurasiSistemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\RencanaPerbaikanController;
use App\Http\Controllers\NavigasiController;
// ============================================
// RUTE PUBLIK
// ============================================
Route::get('/', [BerandaController::class, 'index'])->name('beranda');
Route::get('/tentang', [BerandaController::class, 'tentang'])->name('tentang');

Route::post(
    '/newsletter/subscribe',
    [BerandaController::class, 'subscribeNewsletter']
)->name('newsletter.subscribe');

Route::get(
    '/peta',
    [PetaController::class, 'index']
)->name('peta.index');

Route::get(
    '/peta/data',
    [PetaController::class, 'getLaporanData']
)->name('peta.data');

Route::get(
    '/peta/fasilitas',
    [PetaController::class, 'getFasilitasData']
)->name('peta.fasilitas');

// ============================================
// AUTENTIKASI
// ============================================
Route::get(
    '/login',
    [AuthController::class, 'showLoginForm']
)->name('login');

Route::post(
    '/login',
    [AuthController::class, 'login']
)->middleware('throttle:6,1')->name('login.post');

Route::get(
    '/register',
    [AuthController::class, 'showRegisterForm']
)->name('auth.register');

Route::post(
    '/register',
    [AuthController::class, 'register']
)->middleware('throttle:5,10')->name('register.post');

Route::post(
    '/logout',
    [AuthController::class, 'logout']
)->name('logout');

Route::get(
    '/auth/google',
    [AuthController::class, 'redirectToGoogle']
)->name('auth.google');

Route::get(
    '/auth/google/callback',
    [AuthController::class, 'handleGoogleCallback']
)->name('auth.google.callback');

Route::get(
    '/forgot-password',
    [ForgotPasswordController::class, 'showLinkRequestForm']
)->name('password.request');

Route::post(
    '/forgot-password',
    [ForgotPasswordController::class, 'sendResetLinkEmail']
)->name('password.email');

Route::get(
    '/reset-password/{token}',
    [ForgotPasswordController::class, 'showResetForm']
)->name('password.reset');

Route::post(
    '/reset-password',
    [ForgotPasswordController::class, 'resetPassword']
)->name('password.update');

// ============================================
// RUTE PENGGUNA TERAUTENTIKASI
// ============================================
Route::middleware('auth')->group(function () {
    Route::resource('laporan', LaporanController::class)
        ->except(['index']);

    Route::get(
        '/laporan',
        [LaporanController::class, 'index']
    )->name('laporan.index');

    Route::get(
        '/notifikasi',
        [NotifikasiController::class, 'index']
    )->name('notifikasi.index');

    Route::post(
        '/notifikasi/{notifikasi}/baca',
        [NotifikasiController::class, 'markAsRead']
    )->name('notifikasi.read');

    Route::post(
        '/notifikasi/baca-semua',
        [NotifikasiController::class, 'markAllAsRead']
    )->name('notifikasi.read-all');

    Route::get(
        '/notifikasi/belum-dibaca',
        [NotifikasiController::class, 'unreadCount']
    )->name('notifikasi.unread-count');

    Route::get(
        '/profil',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::put(
        '/profil',
        [ProfileController::class, 'update']
    )->name('profile.update');
});

// ============================================
// ADMINISTRATOR SAJA
// ============================================
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard
        Route::get(
            '/dashboard',
            [DashboardController::class, 'index']
        )->name('dashboard');

        Route::get(
            '/dashboard/chart',
            [DashboardController::class, 'getChartData']
        )->name('dashboard.chart');

        // Sprint 2: Verifikasi laporan
        Route::get(
            '/verifikasi',
            [VerifikasiController::class, 'index']
        )->name('verifikasi.index');

        Route::get(
            '/verifikasi/{laporan}',
            [VerifikasiController::class, 'show']
        )->name('verifikasi.show');

        Route::post(
            '/verifikasi/{laporan}/setujui',
            [VerifikasiController::class, 'approve']
        )->name('verifikasi.approve');

        Route::post(
            '/verifikasi/{laporan}/tolak',
            [VerifikasiController::class, 'reject']
        )->name('verifikasi.reject');

        // Fitur verifikasi lama tetap dipertahankan.
        Route::post(
            '/verifikasi/{laporan}/kembalikan',
            [VerifikasiController::class, 'return']
        )->name('verifikasi.return');

        Route::post(
            '/verifikasi/{laporan}/perbaikan',
            [VerifikasiController::class, 'markInProgress']
        )->name('verifikasi.in-progress');

        Route::post(
            '/verifikasi/{laporan}/selesai',
            [VerifikasiController::class, 'markCompleted']
        )->name('verifikasi.completed');

        // Sprint 2: CRUD Kategori Hambatan
        Route::resource(
            'kategori-hambatan',
            KategoriHambatanController::class
        )->parameters([
            'kategori-hambatan' => 'kategoriHambatan',
        ]);

        // Modul admin yang sudah ada.
        Route::resource(
            'fasilitas-publik',
            FasilitasPublikController::class
        )->parameters([
            'fasilitas-publik' => 'fasilitasPublik',
        ]);

        Route::resource(
            'pengaturan-prioritas',
            PengaturanPrioritasController::class
        )->parameters([
            'pengaturan-prioritas' => 'pengaturanPrioritas',
        ]);

        Route::post(
            '/pengaturan-prioritas/{pengaturanPrioritas}/aktifkan',
            [PengaturanPrioritasController::class, 'activate']
        )->name('pengaturan-prioritas.activate');

        Route::post(
            '/pengaturan-prioritas/hitung-ulang',
            [PengaturanPrioritasController::class, 'recalculate']
        )->name('pengaturan-prioritas.recalculate');

        Route::resource('wilayah', WilayahController::class);

        Route::resource(
            'konfigurasi-sistem',
            KonfigurasiSistemController::class
        )->except(['create', 'show', 'destroy'])
            ->parameters([
                'konfigurasi-sistem' => 'konfigurasiSistem',
            ]);

        Route::resource('user', UserController::class);

        Route::get(
            '/audit',
            [AuditController::class, 'index']
        )->name('audit.index');

        Route::get(
            '/audit/{audit}',
            [AuditController::class, 'show']
        )->name('audit.show');
    });

// ============================================
// DINAS
// ============================================
Route::middleware(['auth', 'dinas'])
    ->prefix('dinas')
    ->name('dinas.')
    ->group(function () {
          Route::get('/dashboard', [DashboardController::class, 'indexDinas'])
            ->name('dashboard');

        Route::get('/laporan', [LaporanController::class, 'indexDinas'])
            ->name('laporan.index');

        Route::get('/laporan/unduh', [LaporanController::class, 'unduh'])
            ->name('laporan.unduh');
            Route::get(
    '/laporan/{id}/pdf',
    [LaporanController::class, 'unduhPdf']
)->name('laporan.pdf');

    });

    //rencana perbaikan
      Route::get(
            '/rencana-perbaikan',
            [RencanaPerbaikanController::class, 'index']
        )->name('rencana perbaikan.index');

        //nearby
        Route::get('/peta/nearby', [PetaController::class, 'nearby'])
    ->name('peta.nearby');

    //navigasi
    //navigasi
     Route::get('/navigasi', [NavigasiController::class, 'index'])
    ->name('navigasi.index');
    //tujuan
Route::post('/navigasi/cari-tujuan', [NavigasiController::class, 'cariTujuan'])
    ->name('navigasi.cari-tujuan');
    //rute
    Route::post('/navigasi/rute', [NavigasiController::class, 'rute'])
    ->name('navigasi.rute');
    //cek hambatam
    Route::post('/navigasi/cek-hambatan', [NavigasiController::class, 'cekHambatanRute'])
    ->name('navigasi.cek-hambatan');
   
// ============================================
// WARGA
// ============================================
Route::middleware('auth')
    ->prefix('warga')
    ->name('warga.')
    ->group(function () {
        Route::get(
            '/dashboard',
            [DashboardController::class, 'indexWarga']
        )->name('dashboard');
    });

   