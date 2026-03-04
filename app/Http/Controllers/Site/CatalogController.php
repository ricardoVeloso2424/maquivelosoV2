<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $category = (string) $request->get('category', '');
        $sort = (string) $request->get('sort', 'name');
        $dir = (string) $request->get('dir', 'asc');

        if (!in_array($sort, ['name', 'price'], true)) {
            $sort = 'name';
        }

        if (!in_array($dir, ['asc', 'desc'], true)) {
            $dir = 'asc';
        }

        if ($sort === 'name') {
            $dir = 'asc';
        }

        $priceMin = trim((string) $request->get('price_min', ''));
        $priceMax = trim((string) $request->get('price_max', ''));

        // Backward compatibility with existing links that still use ?price=...
        if ($priceMax === '') {
            $priceMax = trim((string) $request->get('price', ''));
        }

        $parsedMinPrice = $this->parsePriceFilter($priceMin);
        $parsedMaxPrice = $this->parsePriceFilter($priceMax);

        if ($parsedMinPrice !== null && $parsedMaxPrice !== null && $parsedMinPrice > $parsedMaxPrice) {
            [$parsedMinPrice, $parsedMaxPrice] = [$parsedMaxPrice, $parsedMinPrice];
        }

        $machinesQuery = Machine::query()
            ->with([
                'category:id,name',
                'firstImage:id,machine_id,path,sort_order',
            ])
            ->where('status', 'available')
            ->when($q !== '', fn ($query) => $query->where('name', 'like', "%{$q}%"))
            ->when($category !== '', fn ($query) => $query->where('category_id', $category))
            ->when($parsedMinPrice !== null, fn ($query) => $query->whereNotNull('price')->where('price', '>=', $parsedMinPrice))
            ->when($parsedMaxPrice !== null, fn ($query) => $query->whereNotNull('price')->where('price', '<=', $parsedMaxPrice));

        if ($sort === 'price') {
            $machinesQuery
                ->orderByRaw('CASE WHEN price IS NULL THEN 1 ELSE 0 END')
                ->orderBy('price', $dir)
                ->orderBy('name', 'asc');
        } else {
            $machinesQuery->orderBy('name', 'asc');
        }

        $machines = $machinesQuery
            ->paginate(12)
            ->withQueryString();

        $categories = \App\Models\Category::query()->orderBy('name')->get(['id', 'name']);

        return view('site.catalog', compact('machines', 'categories', 'q', 'category', 'priceMin', 'priceMax', 'sort', 'dir'));
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
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        $normalized = preg_replace('/[^\d,.\s]/u', '', $value);
        if ($normalized === null) {
            return null;
        }

        $normalized = preg_replace('/\s+/u', '', $normalized);
        if ($normalized === null || $normalized === '') {
            return null;
        }

        $lastComma = strrpos($normalized, ',');
        $lastDot = strrpos($normalized, '.');

        $decimalSeparator = null;

        if ($lastComma !== false && $lastDot !== false) {
            $decimalSeparator = $lastComma > $lastDot ? ',' : '.';
        } elseif ($lastComma !== false && $this->looksLikeDecimalSeparator($normalized, ',')) {
            $decimalSeparator = ',';
        } elseif ($lastDot !== false && $this->looksLikeDecimalSeparator($normalized, '.')) {
            $decimalSeparator = '.';
        }

        if ($decimalSeparator === ',') {
            $normalized = str_replace('.', '', $normalized);
            $normalized = str_replace(',', '.', $normalized);
        } elseif ($decimalSeparator === '.') {
            $normalized = str_replace(',', '', $normalized);
        } else {
            $normalized = str_replace([',', '.'], '', $normalized);
        }

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $normalized)) {
            return null;
        }

        return (float) $normalized;
    }

    private function looksLikeDecimalSeparator(string $value, string $separator): bool
    {
        $parts = explode($separator, $value);
        $fraction = end($parts);

        if ($fraction === false) {
            return false;
        }

        $length = strlen($fraction);

        return $length >= 1 && $length <= 2;
    }
}
