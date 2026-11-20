<?php

use App\Http\Controllers\Learner\DashboardController;
use App\Http\Controllers\Learner\ProjectController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'))->name('welcome');

Route::get('/language/{locale}', function (string $locale) {
    if (! in_array($locale, ['en', 'ar'], true)) {
        abort(400);
    }

    session()->put('locale', $locale);

    return redirect()->back();
})->name('language.switch');

Route::middleware(['auth', 'verified', 'active'])->group(function () {

    // Role router: Breeze redirects here after login and registration
    Route::get('/dashboard', function () {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('learner.dashboard');
    })->name('dashboard');

    Route::middleware('learner')->group(function () {
        Route::get('/learner/dashboard', [DashboardController::class, 'index'])
            ->name('learner.dashboard');
    });

    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/', fn () => view('admin.dashboard'))
            ->name('admin.dashboard');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::view('/terms', 'legal.terms')->name('terms');
Route::view('/privacy', 'legal.privacy')->name('privacy');

require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified', 'active', 'learner'])->group(function () {

    // Projects (Phase 3)
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
});
