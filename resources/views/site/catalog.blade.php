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

{{-- Page header --}}
<section class="relative overflow-hidden border-b border-stone-200 bg-white">
    <div class="pattern-dots pointer-events-none absolute inset-0 opacity-60"></div>
    <div class="relative mx-auto flex max-w-7xl flex-col gap-3 px-4 py-12 sm:px-6 lg:px-8 lg:py-14">
        <span class="inline-flex items-center gap-2.5 text-xs font-semibold uppercase tracking-[0.2em] text-brand-700">
            <span class="h-px w-7 stitch-line"></span>Catálogo
        </span>
        <h1 class="font-serif text-4xl font-semibold tracking-tight text-stone-900 sm:text-5xl">
            Máquinas disponíveis
        </h1>
        <p class="max-w-2xl text-base leading-relaxed text-stone-600">
            Explore os modelos disponíveis para compra imediata. Filtre por categoria, preço e ordene como preferir.
        </p>
    </div>
</section>

<div class="mx-auto max-w-7xl space-y-8 px-4 py-10 sm:px-6 lg:px-8">
    {{-- Filters --}}
    <section class="rounded-3xl bg-white p-5 shadow-card ring-1 ring-stone-200/70 sm:p-6">
        <form method="GET" action="{{ route('site.catalog') }}" class="space-y-4">
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">
                <div class="lg:col-span-5">
                    <label for="catalog_search" class="mb-2 block text-sm font-medium text-stone-900">Pesquisar</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-stone-400">
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
                            class="w-full rounded-xl border-stone-300 py-3 pl-12 pr-4 text-sm text-stone-900 transition focus:border-brand-500 focus:ring-brand-500"
                        >
                    </div>
                </div>

                <div class="lg:col-span-3">
                    <label for="catalog_category" class="mb-2 block text-sm font-medium text-stone-900">Categoria</label>
                    <select
                        id="catalog_category"
                        name="category"
                        class="w-full rounded-xl border-stone-300 py-3 text-sm text-stone-900 transition focus:border-brand-500 focus:ring-brand-500"
                    >
                        <option value="">Todas</option>
                        @foreach(($categories ?? []) as $cat)
                            <option value="{{ $cat->id }}" @selected((string)$category === (string)$cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-4">
                    <label for="sort_option" class="mb-2 block text-sm font-medium text-stone-900">Ordenar</label>
                    @php
                        $sortOption = $sort === 'price'
                            ? ($dir === 'desc' ? 'price_desc' : 'price_asc')
                            : 'name_asc';
                    @endphp
                    <select
                        id="sort_option"
                        class="w-full rounded-xl border-stone-300 py-3 text-sm text-stone-900 transition focus:border-brand-500 focus:ring-brand-500"
                    >
                        <option value="name_asc" @selected($sortOption === 'name_asc')>Nome (A-Z)</option>
                        <option value="price_asc" @selected($sortOption === 'price_asc')>Preço: mais barato</option>
                        <option value="price_desc" @selected($sortOption === 'price_desc')>Preço: mais caro</option>
                    </select>
                    <input type="hidden" name="sort" id="sort_field" value="{{ $sort }}">
                    <input type="hidden" name="dir" id="dir_field" value="{{ $dir }}">
                </div>
            </div>

            <details class="group rounded-xl border border-stone-200 bg-stone-50/80" @if($priceMin !== '' || $priceMax !== '') open @endif>
                <summary class="flex cursor-pointer list-none items-center justify-between px-4 py-3 text-sm font-medium text-stone-700">
                    <span class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4 text-stone-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 6h16"></path><path d="M7 12h10"></path><path d="M10 18h4"></path></svg>
                        Filtros de preço
                    </span>
                    <svg class="h-4 w-4 text-stone-400 transition group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 9l6 6 6-6"></path></svg>
                </summary>
                <div class="grid grid-cols-1 gap-4 px-4 pb-4 sm:grid-cols-2">
                    <div>
                        <label for="catalog_price_min" class="mb-2 block text-sm font-medium text-stone-900">Preço mínimo</label>
                        <input
                            id="catalog_price_min"
                            name="price_min"
                            value="{{ $priceMin }}"
                            inputmode="decimal"
                            placeholder="Ex.: 500"
                            class="w-full rounded-xl border-stone-300 px-4 py-3 text-sm text-stone-900 transition focus:border-brand-500 focus:ring-brand-500"
                        >
                    </div>
                    <div>
                        <label for="catalog_price_max" class="mb-2 block text-sm font-medium text-stone-900">Preço máximo</label>
                        <input
                            id="catalog_price_max"
                            name="price_max"
                            value="{{ $priceMax }}"
                            inputmode="decimal"
                            placeholder="Ex.: 1200"
                            class="w-full rounded-xl border-stone-300 px-4 py-3 text-sm text-stone-900 transition focus:border-brand-500 focus:ring-brand-500"
                        >
                    </div>
                </div>
            </details>

            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-stone-100 pt-4">
                <p class="text-sm text-stone-500">
                    <span class="font-semibold text-stone-900">{{ $resultCount }}</span> resultado(s)
                    @if($hasFilters)<span class="text-stone-400">· filtros aplicados</span>@endif
                </p>
                <div class="flex flex-wrap items-center gap-2">
                    @if($hasFilters)
                        <x-ui.button :href="route('site.catalog')" variant="ghost" size="sm">Limpar filtros</x-ui.button>
                    @endif
                    <x-ui.button type="submit" variant="dark" size="md">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="M21 21l-4.3-4.3"></path></svg>
                        Aplicar filtros
                    </x-ui.button>
                </div>
            </div>
        </form>
    </section>

    {{-- Results --}}
    @if($resultCount > 0 || ($machines ?? collect())->count() > 0)
        <section class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
            @foreach($machines as $i => $machine)
                <div data-reveal data-reveal-delay="{{ ($i % 3) + 1 }}">
                    <x-machine.card :machine="$machine" />
                </div>
            @endforeach
        </section>
    @else
        <x-ui.empty-state title="Nenhuma máquina encontrada">
            Ajuste os filtros ou limpe a pesquisa para ver mais resultados.
            <x-slot:action>
                <x-ui.button :href="route('site.catalog')" variant="dark" size="md">Ver todas as máquinas</x-ui.button>
            </x-slot:action>
        </x-ui.empty-state>
    @endif

    @if(isset($machines) && method_exists($machines, 'links'))
        <div class="pt-2">
            {{ $machines->appends(request()->query())->links() }}
        </div>
    @endif
</div>

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
