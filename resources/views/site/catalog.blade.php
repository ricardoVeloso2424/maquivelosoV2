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
@endphp

<div class="mx-auto max-w-7xl space-y-8 px-4 py-8 sm:px-6 lg:px-8">
    <section class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 sm:p-6">
        <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Catálogo</h1>
                <p class="mt-1 text-sm text-slate-600">Encontre máquinas disponíveis para compra imediata.</p>
            </div>
            <div class="text-sm text-slate-500">
                @if(isset($machines))
                    {{ method_exists($machines, 'total') ? $machines->total() : $machines->count() }} resultado(s)
                @endif
            </div>
        </div>

        <form method="GET" action="{{ route('site.catalog') }}" class="space-y-4">
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">
                <div class="lg:col-span-5">
                    <label for="catalog_search" class="mb-2 block text-sm font-medium text-slate-900">Pesquisar</label>
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
                            class="w-full rounded-xl border-slate-300 py-3 pl-12 pr-4 text-sm text-slate-900 focus:border-slate-900 focus:ring-slate-900"
                        />
                    </div>
                </div>

                <div class="lg:col-span-3">
                    <label for="catalog_category" class="mb-2 block text-sm font-medium text-slate-900">Categoria</label>
                    <select
                        id="catalog_category"
                        name="category"
                        class="w-full rounded-xl border-slate-300 py-3 text-sm text-slate-900 focus:border-slate-900 focus:ring-slate-900"
                    >
                        <option value="">Todas</option>
                        @foreach(($categories ?? []) as $cat)
                            <option value="{{ $cat->id }}" @selected((string)$category === (string)$cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-4">
                    <label for="sort_option" class="mb-2 block text-sm font-medium text-slate-900">Ordenar</label>
                    @php
                        $sortOption = $sort === 'price'
                            ? ($dir === 'desc' ? 'price_desc' : 'price_asc')
                            : 'name_asc';
                    @endphp
                    <select
                        id="sort_option"
                        class="w-full rounded-xl border-slate-300 py-3 text-sm text-slate-900 focus:border-slate-900 focus:ring-slate-900"
                    >
                        <option value="name_asc" @selected($sortOption === 'name_asc')>Nome (A-Z)</option>
                        <option value="price_asc" @selected($sortOption === 'price_asc')>Preço: mais barato</option>
                        <option value="price_desc" @selected($sortOption === 'price_desc')>Preço: mais caro</option>
                    </select>
                    <input type="hidden" name="sort" id="sort_field" value="{{ $sort }}">
                    <input type="hidden" name="dir" id="dir_field" value="{{ $dir }}">
                </div>
            </div>

            <details class="rounded-xl border border-slate-200 bg-slate-50/80" @if($priceMin !== '' || $priceMax !== '') open @endif>
                <summary class="cursor-pointer list-none px-4 py-3 text-sm font-medium text-slate-700">
                    Mais filtros
                </summary>
                <div class="grid grid-cols-1 gap-4 px-4 pb-4 sm:grid-cols-2">
                    <div>
                        <label for="catalog_price_min" class="mb-2 block text-sm font-medium text-slate-900">Preço mínimo</label>
                        <input
                            id="catalog_price_min"
                            name="price_min"
                            value="{{ $priceMin }}"
                            placeholder="Ex.: 500"
                            class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-slate-900 focus:ring-slate-900"
                        />
                    </div>

                    <div>
                        <label for="catalog_price_max" class="mb-2 block text-sm font-medium text-slate-900">Preço máximo</label>
                        <input
                            id="catalog_price_max"
                            name="price_max"
                            value="{{ $priceMax }}"
                            placeholder="Ex.: 1200"
                            class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-slate-900 focus:ring-slate-900"
                        />
                    </div>
                </div>
            </details>

            <div class="flex flex-wrap items-center justify-end gap-3">
                <a href="{{ route('site.catalog') }}"
                   class="rounded text-sm font-medium text-slate-600 transition hover:text-slate-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2">
                    Limpar filtros
                </a>
                <button class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2">
                    Aplicar filtros
                </button>
            </div>
        </form>
    </section>

    <section class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
        @forelse(($machines ?? []) as $machine)
            @php
                $imgUrl = $machine->main_image?->thumb_url;

                $name = $machine->name ?? '—';

                $priceText = $machine->price_formatted;
                $state = $machine->priceState();

                $showNegotiable = in_array($state, ['price_negotiable', 'negotiable'], true);
            @endphp

            <a href="{{ route('site.machine.show', $machine) }}"
               class="group overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2">
                <div class="relative aspect-[5/4] overflow-hidden bg-slate-100">
                    @if($imgUrl)
                        <img src="{{ $imgUrl }}" alt="{{ $name }}" width="500" height="400" loading="lazy" decoding="async" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]">
                    @else
                        <div class="flex h-full w-full items-center justify-center text-slate-400">
                            <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                                <path d="M3 16l5-5 4 4 3-3 6 6"></path>
                                <path d="M14 8h.01"></path>
                            </svg>
                        </div>
                    @endif

                    @if($showNegotiable)
                        <span class="absolute left-3 top-3 inline-flex items-center rounded-full bg-emerald-500 px-2.5 py-1 text-xs font-semibold text-white">
                            Negociável
                        </span>
                    @endif
                </div>

                <div class="p-5">
                    <h2 class="line-clamp-1 text-lg font-semibold text-slate-900">{{ $name }}</h2>

                    <div class="mt-2 min-h-[2.2rem]">
                        @if($state === 'on_request')
                            <p class="text-sm font-medium text-slate-500">Sob consulta</p>
                        @elseif($state === 'negotiable')
                            <p class="text-sm font-medium text-emerald-700">Preço negociável</p>
                        @else
                            <p class="text-2xl font-bold tracking-tight text-slate-900">{{ $priceText }}</p>
                        @endif
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
                <h2 class="text-lg font-semibold text-slate-900">Nenhuma máquina encontrada</h2>
                <p class="mt-2 text-sm text-slate-600">Ajuste os filtros ou limpe a pesquisa para ver mais resultados.</p>
                <a href="{{ route('site.catalog') }}"
                   class="mt-4 inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2">
                    Ver todas as máquinas
                </a>
            </div>
        @endforelse
    </section>

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
