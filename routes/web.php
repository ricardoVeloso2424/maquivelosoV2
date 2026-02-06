<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Site\CatalogController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MachineController;
use App\Http\Controllers\Admin\MachineImageController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SettingsController;

/*
|--------------------------------------------------------------------------
| Site público
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('site.home'))->name('site.home');

// Catálogo (lista)
Route::get('/catalogo', [CatalogController::class, 'index'])->name('site.catalog');

// Detalhe da máquina
Route::get('/catalogo/{machine}', [CatalogController::class, 'show'])->name('site.machine.show');

Route::get('/contacto', fn () => view('site.contact'))->name('site.contact');

/*
|--------------------------------------------------------------------------
| “Dashboard” default do Breeze -> redireciona para o admin
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', fn () => redirect()->route('admin.dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Perfil (Breeze)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Backoffice
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->as('admin.')
    ->scopeBindings()
    ->group(function () {

        Route::get('/', DashboardController::class)->name('dashboard');

        Route::resource('machines', MachineController::class);

        Route::patch('machines/{machine}/status', [MachineController::class, 'updateStatus'])
            ->name('machines.updateStatus');

        Route::delete('machines/{machine}/images/{image}', [MachineImageController::class, 'destroy'])
            ->name('machines.images.destroy');

        Route::resource('categories', CategoryController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        Route::get('settings', [SettingsController::class, 'edit'])->name('settings');
        Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
    });

require __DIR__ . '/auth.php';
