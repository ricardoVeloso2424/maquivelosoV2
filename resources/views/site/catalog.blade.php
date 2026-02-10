@extends('layouts.site')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;

    $imgUrlFrom = function ($img) {
        if (!$img) return null;

        $path = $img->path ?? null;
        if (!$path) return null;

        if (str_starts_with($path, 'http')) return $path;

        $path = ltrim($path, '/');
        if (str_starts_with($path, 'public/')) $path = substr($path, 7);

        return Storage::url($path);
    };

    $money = function ($value) {
        if ($value === null || $value === '') return null;
        return number_format((float)$value, 0, ',', '.') . ' €';
    };

    $q        = $q        ?? request('q', '');
    $category = $category ?? request('category', '');
    $price    = $price    ?? request('price', '');
@endphp

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <div>
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-gray-900">Catálogo</h1>
        <p class="mt-2 text-sm text-gray-600">
            Mostra 1 foto, nome e preço. Detalhes só ao abrir a máquina.
        </p>
    </div>

    <div class="rounded-2xl border border-gray-100 bg-white p-5 sm:p-6 shadow-sm">
        <form method="GET" action="{{ route('site.catalog') }}" class="space-y-4">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                <div class="lg:col-span-7">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Pesquisar</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-gray-400">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="7"></circle>
                                <path d="M21 21l-4.3-4.3"></path>
                            </svg>
                        </span>
                        <input
                            name="q"
                            value="{{ $q }}"
                            placeholder="Nome..."
                            class="w-full rounded-xl border-gray-200 bg-white py-3 pl-12 pr-4 text-sm shadow-sm focus:border-gray-900 focus:ring-gray-900"
                        />
                    </div>
                </div>

                <div class="lg:col-span-3">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Categoria</label>
                    <select
                        name="category"
                        class="w-full rounded-xl border-gray-200 bg-white py-3 text-sm shadow-sm focus:border-gray-900 focus:ring-gray-900"
                    >
                        <option value="">Todas</option>
                        @foreach(($categories ?? []) as $cat)
                            <option value="{{ $cat->id }}" @selected((string)$category === (string)$cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Preço</label>
                    <input
                        name="price"
                        value="{{ $price }}"
                        placeholder="(opcional)"
                        class="w-full rounded-xl border-gray-200 bg-white py-3 px-4 text-sm shadow-sm focus:border-gray-900 focus:ring-gray-900"
                    />
                </div>
            </div>

            <div class="flex items-center justify-between gap-4">
                <div class="text-sm text-gray-500">
                    @if(isset($machines))
                        {{ method_exists($machines, 'total') ? $machines->total() : $machines->count() }} resultado(s)
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('site.catalog') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">
                        Limpar
                    </a>
                    <button class="rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-gray-800">
                        Filtrar
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse(($machines ?? []) as $machine)
            @php
                $firstImg = $machine->images->first() ?? null;
                $imgUrl = $imgUrlFrom($firstImg);

                $name = $machine->name ?? '—';

                $priceText = $money($machine->price);
                $isNegotiable = (bool)($machine->negotiable ?? false);

                $showPrice = (bool)$priceText;
                $showNegotiable = $isNegotiable;

                $showSobConsulta = !$showPrice && !$showNegotiable;
            @endphp

            <a href="{{ route('site.machine.show', $machine) }}"
               class="group rounded-2xl border border-gray-100 bg-white shadow-sm hover:shadow-md transition overflow-hidden">
                <div class="aspect-[4/3] bg-gray-100 overflow-hidden">
                    @if($imgUrl)
                        <img src="{{ $imgUrl }}" alt="{{ $name }}" class="h-full w-full object-cover group-hover:scale-[1.02] transition">
                    @else
                        <div class="h-full w-full flex items-center justify-center text-gray-400">
                            <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                                <path d="M3 16l5-5 4 4 3-3 6 6"></path>
                                <path d="M14 8h.01"></path>
                            </svg>
                        </div>
                    @endif
                </div>

                <div class="p-5">
                    <div class="font-semibold text-gray-900 truncate">{{ $name }}</div>

                    <div class="mt-1 text-sm">
                        @if($showSobConsulta)
                            <span class="text-gray-500">Sob consulta</span>
                        @else
                            <div class="flex flex-wrap items-center gap-2">
                                @if($showPrice)
                                    <span class="font-semibold text-gray-900">{{ $priceText }}</span>
                                @endif

                                @if($showNegotiable)
                                    <span class="inline-flex items-center rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">
                                        Negociável
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="mt-4 text-xs text-gray-500">Ver detalhes</div>
                </div>
            </a>
        @empty
            <div class="col-span-full rounded-2xl border border-gray-100 bg-white p-10 text-center text-gray-600">
                Não há máquinas disponíveis.
            </div>
        @endforelse
    </div>

    @if(isset($machines) && method_exists($machines, 'links'))
        <div class="pt-2">
            {{ $machines->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
