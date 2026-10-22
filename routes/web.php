<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Site\CatalogController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MachineController;
use App\Http\Controllers\Admin\MachineImageController;
use App\Models\Category;
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
    $categories = collect();
    $catalogPicks = collect();

    try {
        if (Schema::hasTable('machines')) {
            $imageRelations = [
                'firstImage:id,machine_id,path,thumb_path,sort_order',
                'featuredImage:id,machine_id,path,thumb_path,sort_order,is_featured',
            ];

            $featuredMachines = Machine::query()
                ->with(array_merge($imageRelations, ['category:id,name']))
                ->where('featured', true)
                ->where('status', 'available')
                ->latest()
                ->limit(6)
                ->get();

            // Real categories with available products, each shown with one product photo.
            $categories = Category::query()
                ->withCount(['machines as available_count' => fn ($query) => $query->where('status', 'available')])
                ->orderBy('name')
                ->get()
                ->filter(fn (Category $category) => $category->available_count > 0)
                ->values();

            $pickFromCategory = fn (Category $category, int $limit) => Machine::query()
                ->with(array_merge($imageRelations, ['category:id,name']))
                ->where('category_id', $category->id)
                ->where('status', 'available')
                ->has('images')
                // Prefer products with higher-resolution photos (those large enough to get a thumbnail).
                ->withExists(['images as has_large_photo' => fn ($query) => $query->whereNotNull('thumb_path')])
                ->orderByDesc('has_large_photo')
                ->latest()
                ->limit($limit)
                ->get();

            $categories->each(fn (Category $category) => $category->setRelation('cover', $pickFromCategory($category, 1)->first()));

            // Without featured machines, show a small cross-category selection instead of an empty block.
            if ($featuredMachines->isEmpty()) {
                // Alternate categories so the first row shows one product of each.
                $perCategory = $categories->map(fn (Category $category) => $pickFromCategory($category, 2));
                $catalogPicks = collect(range(0, 1))
                    ->flatMap(fn (int $i) => $perCategory->map(fn ($machines) => $machines->get($i)))
                    ->filter()
                    ->take(6)
                    ->values();
            }
        }
    } catch (\Throwable) {
        $featuredMachines = $categories = $catalogPicks = collect();
    }

    return view('site.home', compact('featuredMachines', 'categories', 'catalogPicks'));
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

        Route::patch('machines/{machine}/images/{image}/feature', [MachineImageController::class, 'feature'])
            ->name('machines.images.feature');

        Route::resource('categories', CategoryController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        Route::get('settings', [SettingsController::class, 'edit'])->name('settings');
        Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
    });

require __DIR__ . '/auth.php';
