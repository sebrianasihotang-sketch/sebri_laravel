<?php

use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\CatatanController;
use App\Http\Controllers\HaloController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BerandaController::class, 'index'])->name('home');
Route::get('/halo', [HaloController::class, 'index'])->name('halo');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::view('/kontak', 'kontak')->name('kontak');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('throttle:6,1');
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirectToProvider'])
        ->whereIn('provider', ['github', 'google'])
        ->name('social.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback'])
        ->whereIn('provider', ['github', 'google'])
        ->name('social.callback');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/catatan', [CatatanController::class, 'index'])->name('catatan.index');
    Route::get('/catatan/tambah', [CatatanController::class, 'create'])->name('catatan.create');
    Route::post('/catatan', [CatatanController::class, 'store'])->name('catatan.store');
    Route::get('/catatan/arsip', [CatatanController::class, 'arsip'])->name('catatan.arsip');
    Route::post('/catatan/{id}/archive', [CatatanController::class, 'archive'])->name('catatan.archive');
    Route::post('/catatan/{id}/unarchive', [CatatanController::class, 'unarchive'])->name('catatan.unarchive');
    Route::get('/catatan/{id}/lampiran', [CatatanController::class, 'downloadAttachment'])
        ->name('catatan.attachment');
    Route::get('/catatan/export', [CatatanController::class, 'export'])->name('catatan.export');
    Route::get('/catatan/import', [CatatanController::class, 'importForm'])->name('catatan.import');
    Route::post('/catatan/import', [CatatanController::class, 'import'])->name('catatan.import.store');
    Route::get('/catatan/{id}/edit', [CatatanController::class, 'edit'])->name('catatan.edit');
    Route::get('/catatan/{id}/hapus', [CatatanController::class, 'confirmDelete'])->name('catatan.confirm-delete');
    Route::put('/catatan/{id}', [CatatanController::class, 'update'])->name('catatan.update');
    Route::delete('/catatan/{id}', [CatatanController::class, 'destroy'])->name('catatan.destroy');
    Route::get('/catatan/{id}', [CatatanController::class, 'show'])->name('catatan.show');
});