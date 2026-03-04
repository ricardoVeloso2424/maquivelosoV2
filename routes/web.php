<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Site\CatalogController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MachineController;
use App\Http\Controllers\Admin\MachineImageController;
use App\Models\Machine;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SettingsController;

/*
|--------------------------------------------------------------------------
| Site público
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    $featuredMachines = collect();

    try {
        if (Schema::hasTable('machines')) {
            $featuredMachines = Machine::query()
                ->with(['firstImage:id,machine_id,path,sort_order'])
                ->where('featured', true)
                ->where('status', 'available')
                ->latest()
                ->limit(6)
                ->get();
        }
    } catch (\Throwable) {
        $featuredMachines = collect();
    }

    return view('site.home', compact('featuredMachines'));
})->name('site.home');

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
Route::get('/dashboard', function (Request $request) {
    return $request->user()->is_admin
        ? redirect()->route('admin.dashboard')
        : redirect()->route('site.home');
})
    ->middleware(['auth'])
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
Route::middleware(['auth', 'admin'])
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
