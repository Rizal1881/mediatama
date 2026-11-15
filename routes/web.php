<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\VideoController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return auth()->user()->role === 'admin'
        ? redirect('/admin')
        : redirect()->route('customer.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::view('/dashboard', 'customer.dashboard')->name('dashboard');
    Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
    Route::post('/videos/{video}/request', [VideoController::class, 'requestAccess'])->name('videos.request');
    Route::get('/videos/{video}/stream', [VideoController::class, 'stream'])->name('videos.stream');
        Route::get('/videos/{video}/watch', [VideoController::class, 'watch'])->name('videos.watch');
});

require __DIR__.'/auth.php';