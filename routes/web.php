<?php
// routes/web.php

use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController;
use Illuminate\Support\Facades\Route;

// ========== USER ROUTES (Tanpa Login) ==========
Route::get('/', [PaymentController::class, 'index'])->name('home');
Route::get('/tagihan/{keluarga}', [PaymentController::class, 'tagihan'])->name('user.tagihan');
Route::post('/generate-qris/{pembayaran}', [PaymentController::class, 'generateQris'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('generate.qris');
Route::post('/webhook/midtrans', [PaymentController::class, 'webhook'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('webhook.midtrans');

// ========== ADMIN ROUTES (Wajib Login) ==========
Route::prefix('admin')->name('admin.')->group(function () {
    // Login routes (tanpa middleware admin & tanpa CSRF untuk prevent 419)
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
        ->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])
        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
        ->name('logout');

    // Protected routes (pakai middleware admin)
    Route::middleware(['admin'])->group(function () {
        // Dashboard
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

        // CRUD Keluarga
        Route::get('/keluarga', [AdminController::class, 'keluargaIndex'])->name('keluarga');
        Route::get('/keluarga/create', [AdminController::class, 'keluargaCreate'])->name('keluarga.create');
        Route::post('/keluarga', [AdminController::class, 'keluargaStore'])->name('keluarga.store');
        Route::get('/keluarga/{keluarga}/edit', [AdminController::class, 'keluargaEdit'])->name('keluarga.edit');
        Route::put('/keluarga/{keluarga}', [AdminController::class, 'keluargaUpdate'])->name('keluarga.update');
        Route::delete('/keluarga/{keluarga}', [AdminController::class, 'keluargaDestroy'])->name('keluarga.destroy');

        // CRUD Tagihan
        Route::get('/tagihan', [AdminController::class, 'tagihanIndex'])->name('tagihan');
        Route::get('/tagihan/create', [AdminController::class, 'tagihanCreate'])->name('tagihan.create');
        Route::post('/tagihan', [AdminController::class, 'tagihanStore'])->name('tagihan.store');
        Route::get('/tagihan/{pembayaran}/edit', [AdminController::class, 'tagihanEdit'])->name('tagihan.edit');
        Route::put('/tagihan/{pembayaran}', [AdminController::class, 'tagihanUpdate'])->name('tagihan.update');
        Route::delete('/tagihan/{pembayaran}', [AdminController::class, 'tagihanDestroy'])->name('tagihan.destroy');

        // Generate Tagihan Massal
        Route::get('/tagihan/bulk', [AdminController::class, 'generateBulkForm'])->name('tagihan.bulk');
        Route::post('/tagihan/bulk', [AdminController::class, 'generateBulk'])->name('tagihan.bulk.store');

        // Tandai Lunas Manual (exclude CSRF untuk AJAX)
        Route::post('/tagihan/{pembayaran}/mark-paid', [AdminController::class, 'markAsPaid'])
            ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
            ->name('tagihan.mark-paid');

        // Laporan Keuangan
        Route::get('/laporan', [AdminController::class, 'laporan'])->name('laporan');
    });

    // Laporan Keuangan
Route::get('/laporan', [AdminController::class, 'laporan'])->name('laporan');
Route::get('/laporan/export-pdf', [AdminController::class, 'exportPdf'])->name('laporan.export-pdf');
});
