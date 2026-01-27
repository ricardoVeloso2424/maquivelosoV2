<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MachineController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Site público
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('site.home');
})->name('site.home');

Route::get('/catalogo', function () {
    return view('site.catalog');
})->name('site.catalog');

Route::get('/contacto', function () {
    return view('site.contact');
})->name('site.contact');

/*
|--------------------------------------------------------------------------
| Dashboard (OBRIGATÓRIO para o Breeze)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Perfil (Breeze usa profile.edit no menu)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin (protegido por login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {

    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::resource('machines', MachineController::class)->names('admin.machines');

    Route::get('/settings', [SettingsController::class, 'edit'])->name('admin.settings');
    Route::post('/settings', [SettingsController::class, 'update'])->name('admin.settings.update');
});

/*
|--------------------------------------------------------------------------
| Rotas de autenticação (Laravel Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
