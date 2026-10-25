<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\ShortUrlController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Link Management
    Route::get('/links', [ShortUrlController::class, 'index'])->name('short-urls.index');
    Route::get('/links/create', [ShortUrlController::class, 'create'])->name('short-urls.create');
    Route::post('/links', [ShortUrlController::class, 'store'])->name('short-urls.store');
    Route::get('/links/{shortUrl}/edit', [ShortUrlController::class, 'edit'])->name('short-urls.edit');
    Route::put('/links/{shortUrl}', [ShortUrlController::class, 'update'])->name('short-urls.update');
    Route::delete('/links/{shortUrl}', [ShortUrlController::class, 'destroy'])->name('short-urls.destroy');
    Route::get('/links/{shortUrl}/analytics', [ShortUrlController::class, 'analytics'])->name('short-urls.analytics');
});

require __DIR__.'/auth.php';

// Public Short URL Redirection (catch-all at bottom)
Route::get('/{slug}', RedirectController::class)
    ->where('slug', '[A-Za-z0-9_-]+')
    ->name('short-urls.redirect');
