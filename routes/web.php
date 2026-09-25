<?php

use App\Http\Controllers\Admin\AlbumController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DownloadController;
use App\Http\Controllers\Admin\PhotoController as AdminPhotoController;
use App\Http\Controllers\Admin\QrCodeController;
use App\Http\Controllers\BodaController;
use App\Http\Controllers\BodaPhotoController;
use App\Http\Controllers\BodaUploadController;
use App\Http\Controllers\ProjectionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/boda', [BodaController::class, 'show'])->name('boda.show');
Route::get('/boda/subir', [BodaUploadController::class, 'create'])->name('boda.upload.create');
Route::get('/boda/fotos', [BodaPhotoController::class, 'index'])->name('boda.photos.index');
Route::post('/boda/fotos', [BodaUploadController::class, 'store'])
    ->middleware('throttle:photo-uploads')
    ->name('boda.photos.store');
Route::delete('/boda/fotos/{photo}', [BodaPhotoController::class, 'destroy'])->name('boda.photos.destroy');
Route::get('/boda/fotos/{photo}', [BodaPhotoController::class, 'show'])->name('boda.photos.show');
Route::get('/boda/gracias', [BodaUploadController::class, 'thankYou'])->name('boda.upload.thank-you');
Route::get('/boda/proyeccion', [ProjectionController::class, 'showPublic'])->name('boda.projection');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('admin.dashboard');
    })->name('dashboard');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/configuracion', [AlbumController::class, 'edit'])->name('configuration.edit');
    Route::put('/configuracion', [AlbumController::class, 'update'])->name('configuration.update');
    Route::post('/album/toggle-upload', [AlbumController::class, 'toggleUpload'])->name('album.toggle-upload');
    Route::get('/fotos', [AdminPhotoController::class, 'index'])->name('photos.index');
    Route::delete('/fotos/{photo}', [AdminPhotoController::class, 'destroy'])->name('photos.destroy');
    Route::get('/qr', [QrCodeController::class, 'show'])->name('qr.show');
    Route::get('/qr/{format}', [QrCodeController::class, 'download'])->name('qr.download');
    Route::get('/proyeccion', [ProjectionController::class, 'showAdmin'])->name('projection');
    Route::get('/download', [DownloadController::class, 'originals'])->name('download');
});
