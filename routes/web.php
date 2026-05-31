<?php

use App\Http\Controllers\ApplyController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\MockOcrController;
use App\Http\Controllers\SystemAuthController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\TrackController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->name('home');

// Portal content pages (public).
Route::controller(ContentController::class)->group(function () {
    Route::get('/pengenalan', 'pengenalan')->name('pengenalan');
    Route::get('/soalan-lazim', 'soalanLazim')->name('soalan-lazim');
    Route::get('/direktori-cawangan', 'direktoriCawangan')->name('direktori-cawangan');
    Route::get('/perkhidmatan', 'perkhidmatan')->name('perkhidmatan');
});

// Light-theme variant previews (legacy explore)
Route::get('/preview/v1', fn () => view('preview.v1'));
Route::get('/preview/v2', fn () => view('preview.v2'));
Route::get('/preview/v3', fn () => view('preview.v3'));

// Act 1 — Apply (portal, public).
Route::controller(ApplyController::class)->group(function () {
    Route::get('/apply', 'show')->name('apply');
    Route::post('/apply', 'store')->name('apply.store');
});

Route::post('/api/mock-ocr', [MockOcrController::class, 'lookup'])->name('mock-ocr');

// Act 2 — Smart Tracker (portal, public).
Route::controller(TrackController::class)->group(function () {
    Route::get('/track', 'search')->name('track.search');
    Route::post('/track/verify', 'verify')->name('track.verify');
    Route::get('/track/{reference}', 'show')
        ->where('reference', 'APP-[0-9]{8}-[0-9]{4}')
        ->name('track.show');
    Route::get('/api/track/{reference}/status', 'status')
        ->where('reference', 'APP-[0-9]{8}-[0-9]{4}')
        ->name('track.status');
});

// ============== Act 3 — System (officer console, auth required) ==============

// Guest-only login routes
Route::middleware('guest')->group(function () {
    Route::get('/system/login', [SystemAuthController::class, 'showLogin'])->name('system.login');
    Route::post('/system/login', [SystemAuthController::class, 'attempt'])->name('system.login.attempt');
});

// Auth-protected officer routes
Route::middleware('auth')->prefix('system')->group(function () {
    Route::post('/logout', [SystemAuthController::class, 'logout'])->name('system.logout');

    Route::get('/', [SystemController::class, 'utama'])->name('system.utama');
    Route::get('/tapisan', [SystemController::class, 'tapisan'])->name('system.tapisan');
    Route::get('/statistik', [SystemController::class, 'statistik'])->name('system.statistik');
    Route::get('/kanban', [SystemController::class, 'kanban'])->name('system.kanban');
    Route::post('/kanban/move', [SystemController::class, 'kanbanMove'])->name('system.kanban.move');

    Route::get('/pendaftaran/baru', [SystemController::class, 'wizardShow'])->name('system.wizard');
    Route::post('/pendaftaran/baru', [SystemController::class, 'wizardStore'])->name('system.wizard.store');
    Route::get('/audit', [SystemController::class, 'audit'])->name('system.audit');

    Route::get('/tapisan/{reference}', [SystemController::class, 'show'])
        ->where('reference', 'APP-[0-9]{8}-[0-9]{4}')
        ->name('system.tapisan.show');

    Route::post('/tapisan/{reference}/approve', [SystemController::class, 'approve'])
        ->where('reference', 'APP-[0-9]{8}-[0-9]{4}')
        ->name('system.tapisan.approve');

    Route::post('/tapisan/{reference}/reject', [SystemController::class, 'reject'])
        ->where('reference', 'APP-[0-9]{8}-[0-9]{4}')
        ->name('system.tapisan.reject');

    Route::post('/tapisan/bulk-approve', [SystemController::class, 'bulkApprove'])
        ->name('system.tapisan.bulk-approve');

    // Sistem Wajib (LAMPIRAN A) — pitch/demo screens
    Route::get('/abis-match',         [SystemController::class, 'abisMatch'])->name('system.abis');
    Route::get('/biometric-capture',  [SystemController::class, 'biometricCapture'])->name('system.biometric');
    Route::get('/blockchain-ledger',  [SystemController::class, 'blockchainLedger'])->name('system.blockchain');
    Route::get('/kaveat-board',       [SystemController::class, 'kaveatBoard'])->name('system.kaveat');
    Route::get('/upacara-perkahwinan',[SystemController::class, 'upacara'])->name('system.upacara');
    Route::get('/borang-kelahiran',   [SystemController::class, 'borang'])->name('system.borang');
    Route::get('/sijil',              [SystemController::class, 'sijil'])->name('system.sijil');
    Route::get('/agensi-integrasi',   [SystemController::class, 'agensiIntegrasi'])->name('system.agensi');
    Route::get('/sub-fungsi-katalog', [SystemController::class, 'subFungsiKatalog'])->name('system.katalog');
    Route::get('/kafka-events',       [SystemController::class, 'kafkaEvents'])->name('system.kafka');
    Route::get('/hospital-pra-daftar',[SystemController::class, 'hospitalPraDaftar'])->name('system.hospital');
    Route::get('/clms-pipeline',      [SystemController::class, 'clmsPipeline'])->name('system.clms');
    Route::get('/lapor-kehilangan',   [SystemController::class, 'laporKehilangan'])->name('system.lapor');
    Route::get('/kad-mykad',          [SystemController::class, 'cardMyKad'])->name('system.kad');
    Route::get('/family-tree',        [SystemController::class, 'familyTree'])->name('system.familytree');
    Route::get('/mydigital-id',       [SystemController::class, 'mydigitalId'])->name('system.mydigital');
    Route::get('/perkakasan-status',  [SystemController::class, 'perkakasanStatus'])->name('system.perkakasan');
});
