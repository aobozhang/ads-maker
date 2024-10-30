<?php

use App\Http\Controllers\AdsImageController;
use App\Http\Controllers\AdsItemController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin'       => Route::has('login'),
        'canRegister'    => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion'     => PHP_VERSION,
    ]);
});

Route::middleware('auth')->prefix('/')->group(function () {
    Route::resource('/ads-image', AdsImageController::class);
    Route::get('/ads-image/{ads_image}/down', [AdsImageController::class, 'down'])->name('ads-image.down');
    // Route::post('/ads-image/upload', [AdsImageController::class, 'upload'])->name('ads-image.upload');

    Route::resource('/ads-item', AdsItemController::class);
    Route::get('/ads-item/{ads_item}/down', [AdsItemController::class, 'down'])->name('ads-item.down');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
