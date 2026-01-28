<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MachineController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\ProfileController;

Route::get('/', fn () => view('site.home'))->name('site.home');
Route::get('/catalogo', fn () => view('site.catalog'))->name('site.catalog');
Route::get('/contacto', fn () => view('site.contact'))->name('site.contact');

Route::get('/dashboard', fn () => redirect()->route('admin.dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    Route::get('/', fn () => view('admin.dashboard'))->name('admin.dashboard');

    Route::resource('machines', MachineController::class)->names('admin.machines');

    Route::delete('machines/{machine}/images/{image}', [MachineController::class, 'destroyImage'])
        ->name('admin.machines.images.destroy');

    Route::resource('categories', CategoryController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->names('admin.categories');

    Route::get('/settings', [SettingsController::class, 'edit'])->name('admin.settings');
    Route::post('/settings', [SettingsController::class, 'update'])->name('admin.settings.update');
});

require __DIR__ . '/auth.php';
