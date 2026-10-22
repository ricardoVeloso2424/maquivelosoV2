@extends('layouts.site')

@section('content')
@php
    $q        = $q        ?? request('q', '');
    $category = $category ?? request('category', '');
    $priceMin = $priceMin ?? request('price_min', '');
    $priceMax = $priceMax ?? request('price_max', request('price', ''));
    $sort     = $sort     ?? request('sort', 'name');
    $dir      = $dir      ?? request('dir', 'asc');

    if ($sort === 'name') {
        $dir = 'asc';
    }

    $resultCount = isset($machines)
        ? (method_exists($machines, 'total') ? $machines->total() : $machines->count())
        : 0;
    $hasFilters = $q !== '' || (string) $category !== '' || $priceMin !== '' || $priceMax !== '';
@endphp

{{-- Filters --}}
<section class="border-b border-slate-200 bg-slate-50" aria-label="Filtros">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="sr-only">Catálogo</h1>
        <form method="GET" action="{{ route('site.catalog') }}" class="space-y-4">
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">
                <div class="lg:col-span-5">
                    <label for="catalog_search" class="mb-2 block text-sm font-medium text-navy-950">Pesquisar</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <circle cx="11" cy="11" r="7"></circle>
                                <path d="M21 21l-4.3-4.3"></path>
                            </svg>
                        </span>
                        <input
                            id="catalog_search"
                            name="q"
                            value="{{ $q }}"
                            placeholder="Nome da máquina"
                            class="w-full rounded-lg border-slate-300 py-3 text-sm text-navy-950 transition focus:border-navy-700 focus:ring-navy-700 pl-12 pr-4"
                        >
                    </div>
                </div>

                <div class="lg:col-span-3">
                    <label for="catalog_category" class="mb-2 block text-sm font-medium text-navy-950">Categoria</label>
                    <select
                        id="catalog_category"
                        name="category"
                        class="w-full rounded-lg border-slate-300 py-3 text-sm text-navy-950 transition focus:border-navy-700 focus:ring-navy-700"
                    >
                        <option value="">Todas</option>
                        @foreach(($categories ?? []) as $cat)
                            <option value="{{ $cat->id }}" @selected((string)$category === (string)$cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-4">
                    <label for="sort_option" class="mb-2 block text-sm font-medium text-navy-950">Ordenar</label>
                    @php
                        $sortOption = $sort === 'price'
                            ? ($dir === 'desc' ? 'price_desc' : 'price_asc')
                            : 'name_asc';
                    @endphp
                    <select
                        id="sort_option"
                        class="w-full rounded-lg border-slate-300 py-3 text-sm text-navy-950 transition focus:border-navy-700 focus:ring-navy-700"
                    >
                        <option value="name_asc" @selected($sortOption === 'name_asc')>Nome (A-Z)</option>
                        <option value="price_asc" @selected($sortOption === 'price_asc')>Preço: mais barato</option>
                        <option value="price_desc" @selected($sortOption === 'price_desc')>Preço: mais caro</option>
                    </select>
                    <input type="hidden" name="sort" id="sort_field" value="{{ $sort }}">
                    <input type="hidden" name="dir" id="dir_field" value="{{ $dir }}">
                </div>
            </div>

            <details class="group rounded-lg border border-slate-200 bg-white" @if($priceMin !== '' || $priceMax !== '') open @endif>
                <summary class="flex min-h-11 cursor-pointer list-none items-center justify-between px-4 py-3 text-sm font-medium text-navy-950">
                    <span class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4 text-navy-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 6h16"></path><path d="M7 12h10"></path><path d="M10 18h4"></path></svg>
                        Filtros de preço
                    </span>
                    <svg class="h-4 w-4 text-slate-400 transition group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 9l6 6 6-6"></path></svg>
                </summary>
                <div class="grid grid-cols-1 gap-4 px-4 pb-4 sm:grid-cols-2">
                    <div>
                        <label for="catalog_price_min" class="mb-2 block text-sm font-medium text-navy-950">Preço mínimo</label>
                        <input
                            id="catalog_price_min"
                            name="price_min"
                            value="{{ $priceMin }}"
                            inputmode="decimal"
                            placeholder="Ex.: 500"
                            class="w-full rounded-lg border-slate-300 py-3 text-sm text-navy-950 transition focus:border-navy-700 focus:ring-navy-700 px-4"
                        >
                    </div>
                    <div>
                        <label for="catalog_price_max" class="mb-2 block text-sm font-medium text-navy-950">Preço máximo</label>
                        <input
                            id="catalog_price_max"
                            name="price_max"
                            value="{{ $priceMax }}"
                            inputmode="decimal"
                            placeholder="Ex.: 1200"
                            class="w-full rounded-lg border-slate-300 py-3 text-sm text-navy-950 transition focus:border-navy-700 focus:ring-navy-700 px-4"
                        >
                    </div>
                </div>
            </details>

            <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                <p class="text-sm text-slate-600">
                    <span class="font-semibold text-navy-950">{{ $resultCount }}</span> resultado(s)
                    @if($hasFilters)<span class="text-slate-400">· filtros aplicados</span>@endif
                </p>
                <div class="flex flex-wrap items-center gap-4">
                    @if($hasFilters)
                        <a href="{{ route('site.catalog') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-navy-700 underline-offset-4 hover:underline">Limpar filtros</a>
                    @endif
                    <x-ui.button type="submit" variant="navy" shape="rounded" size="md">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="M21 21l-4.3-4.3"></path></svg>
                        Aplicar filtros
                    </x-ui.button>
                </div>
            </div>
        </form>
    </div>
</section>

{{-- Results --}}
<section class="bg-white" aria-label="Resultados">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        @if($resultCount > 0 || ($machines ?? collect())->count() > 0)
            <ul class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 lg:gap-6">
                @foreach($machines as $machine)
                    <li><x-machine.card :machine="$machine" /></li>
                @endforeach
            </ul>
        @else
            <div class="rounded-xl border border-dashed border-slate-300 px-6 py-14 text-center">
                <p class="font-semibold text-navy-950">Nenhuma máquina encontrada</p>
                <p class="mt-2 text-sm text-slate-600">Ajuste os filtros ou limpe a pesquisa para ver mais resultados.</p>
                <x-ui.button :href="route('site.catalog')" variant="navy" shape="rounded" size="md" class="mt-6">Ver todas as máquinas</x-ui.button>
            </div>
        @endif

        @if(isset($machines) && method_exists($machines, 'links'))
            <div class="mt-10">
                {{ $machines->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</section>

<script>
(() => {
    const sortOption = document.getElementById('sort_option');
    const sortField = document.getElementById('sort_field');
    const dirField = document.getElementById('dir_field');

    if (!sortOption || !sortField || !dirField) {
        return;
    }

    const applySortSelection = () => {
        switch (sortOption.value) {
            case 'price_desc':
                sortField.value = 'price';
                dirField.value = 'desc';
                break;
            case 'price_asc':
                sortField.value = 'price';
                dirField.value = 'asc';
                break;
            default:
                sortField.value = 'name';
                dirField.value = 'asc';
        }
    };

    applySortSelection();
    sortOption.addEventListener('change', applySortSelection);
})();
</script>
@endsection
