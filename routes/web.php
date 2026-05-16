<?php

use App\Http\Controllers\App\AccessItemController;
use App\Http\Controllers\App\LinkController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::view('/offline', 'offline')->name('offline');

Route::get('/dashboard', [LinkController::class, 'dashboard'])
    ->middleware(['auth', 'active.user'])
    ->name('dashboard');

Route::get('/app/dashboard', [LinkController::class, 'dashboard'])
    ->middleware(['auth', 'active.user'])
    ->name('app.dashboard');

Route::middleware(['auth', 'active.user'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('app')->name('app.')->group(function () {
        Route::get('/favorites', function () {
            return redirect()->route('app.links.index', ['favorites' => 1]);
        })->name('favorites');

        Route::get('/links/create', [LinkController::class, 'create'])->name('links.create');
        Route::post('/links', [LinkController::class, 'store'])->name('links.store');
        Route::get('/links', [LinkController::class, 'index'])->name('links.index');
        Route::get('/manage', [LinkController::class, 'manage'])->name('manage');
        Route::get('/links/{link}/edit', [LinkController::class, 'edit'])->name('links.edit');
        Route::match(['put', 'patch'], '/links/{link}', [LinkController::class, 'update'])->name('links.update');
        Route::delete('/links/{link}', [LinkController::class, 'destroy'])->name('links.destroy');
        Route::get('/links/{link}/open', [LinkController::class, 'open'])->name('links.open');
        Route::post('/links/{link}/favorite', [LinkController::class, 'toggleFavorite'])->name('links.favorite.toggle');
        Route::get('/access-items', [AccessItemController::class, 'index'])->name('access-items.index');
        Route::get('/access-items/{accessItem}/open', [AccessItemController::class, 'open'])->name('access-items.open');
    });
});

require __DIR__.'/auth.php';
