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
        $category = $request->get('category');
        $price = $request->get('price'); 

        $machines = Machine::query()
            ->with([
                'category:id,name',
                // carrega só a primeira imagem 
                'images' => fn($q) => $q->select('id','machine_id','path')->orderBy('sort_order')->limit(1),
            ])
            ->where('status', 'available') // só disponíveis
            ->when($q, fn($qq) => $qq->where('name', 'like', "%{$q}%"))
            ->when($category, fn($qq) => $qq->where('category_id', $category))
            ->latest()
            ->paginate(12);

        $categories = \App\Models\Category::query()->orderBy('name')->get(['id','name']);

        return view('site.catalog', compact('machines', 'categories', 'q', 'category', 'price'));
    }

    public function show(Machine $machine)
    {

        // abort_if($machine->status !== 'available', 404);

        $machine->load([
            'category:id,name',
            'images:id,machine_id,path,sort_order',
        ]);

        return view('site.machine-show', compact('machine'));
    }
}
