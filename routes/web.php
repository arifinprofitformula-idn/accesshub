<?php

use App\Http\Controllers\App\AccessItemController;
use App\Http\Controllers\App\LinkController;
use App\Http\Controllers\ProfileController;
use App\Models\AccessItem;
use App\Models\Category;
use App\Models\Link;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::view('/offline', 'offline')->name('offline');

Route::get('/dashboard', function () {
    $user = auth()->user();

    $visibleLinks = Link::query()->visibleTo($user);
    $visibleAccessItems = AccessItem::query()->visibleTo($user);

    return view('app.dashboard', [
        'stats' => [
            'active_links' => (clone $visibleLinks)->where('status', 'active')->count(),
            'needs_review_links' => (clone $visibleLinks)->where('status', 'needs_review')->count(),
            'access_items' => (clone $visibleAccessItems)->count(),
            'active_users' => User::query()->where('is_active', true)->count(),
            'categories' => Category::query()->where('is_active', true)->count(),
        ],
        'favorite_links' => Link::query()
            ->visibleTo($user)
            ->withCount('favorites')
            ->orderByDesc('favorites_count')
            ->latest()
            ->take(4)
            ->get(),
        'recent_links' => Link::query()
            ->visibleTo($user)
            ->latest()
            ->take(5)
            ->get(),
        'recent_access_items' => AccessItem::query()
            ->visibleTo($user)
            ->latest()
            ->take(4)
            ->get(),
    ]);
})->middleware(['auth', 'active.user'])->name('dashboard');

Route::get('/app/dashboard', function () {
    return redirect()->route('dashboard');
})->middleware(['auth', 'active.user'])->name('app.dashboard');

Route::middleware(['auth', 'active.user'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('app')->name('app.')->group(function () {
        Route::get('/favorites', function () {
            return redirect()->route('app.links.index', ['favorites' => 1]);
        })->name('favorites');

        Route::get('/links/create', function () {
            abort_unless(auth()->user()?->can('create', \App\Models\Link::class), 403);

            return redirect('/admin/links/create');
        })->name('links.create');

        Route::get('/links', [LinkController::class, 'index'])->name('links.index');
        Route::get('/links/{link}/open', [LinkController::class, 'open'])->name('links.open');
        Route::post('/links/{link}/favorite', [LinkController::class, 'toggleFavorite'])->name('links.favorite.toggle');
        Route::get('/access-items', [AccessItemController::class, 'index'])->name('access-items.index');
        Route::get('/access-items/{accessItem}/open', [AccessItemController::class, 'open'])->name('access-items.open');
    });
});

require __DIR__.'/auth.php';
