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
        $price = trim((string) $request->get('price', ''));
        $maxPrice = $this->parsePriceFilter($price);

        $machines = Machine::query()
            ->with([
                'category:id,name',
                'firstImage:id,machine_id,path,sort_order',
            ])
            ->where('status', 'available')
            ->when($q !== '', fn ($query) => $query->where('name', 'like', "%{$q}%"))
            ->when($category !== '', fn ($query) => $query->where('category_id', $category))
            ->when($maxPrice !== null, fn ($query) => $query->whereNotNull('price')->where('price', '<=', $maxPrice))
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
