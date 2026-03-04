<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $category = (string) $request->get('category', '');
        $price = trim((string) $request->get('price', ''));
        $maxPrice = $this->parsePriceFilter($price);

        $machines = Machine::query()
            ->with([
                'category:id,name',
                'firstImage:id,machine_id,path,sort_order',
            ])
            ->where('status', 'available')
            ->when($q, fn($qq) => $qq->where('name', 'like', "%{$q}%"))
            ->when($category, fn($qq) => $qq->where('category_id', $category))
            ->when($maxPrice !== null, fn ($qq) => $qq->whereNotNull('price')->where('price', '<=', $maxPrice))
            ->latest()
            ->paginate(12);

        $categories = \App\Models\Category::query()->orderBy('name')->get(['id', 'name']);

        return view('site.catalog', compact('machines', 'categories', 'q', 'category', 'price'));
    }

    public function show(Machine $machine)
    {
        abort_if($machine->status !== 'available', 404);

        $machine->load([
            'category:id,name',
            'images:id,machine_id,path,sort_order',
        ]);

        return view('site.machine-show', compact('machine'));
    }

    private function parsePriceFilter(string $value): ?float
    {
        if ($value === '') {
            return null;
        }

        $normalized = str_replace([' ', '€'], '', $value);
        $normalized = str_replace(',', '.', $normalized);

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $normalized)) {
            return null;
        }

        return (float) $normalized;
    }
}
