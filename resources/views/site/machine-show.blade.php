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

    $name = $machine->name ?? '—';
    $priceText = $money($machine->price);
    $categoryName = $machine->category->name ?? null;

    $images = $machine->images ?? collect();
    $main = $machine->featuredImage ?? null;
    if (!$main && $images->count()) $main = $images->first();

    $mainUrl = $imgUrlFrom($main);
@endphp

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <div>
        <a href="{{ route('site.catalog') }}"
           class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-50">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M15 18l-6-6 6-6"></path>
            </svg>
            Voltar ao catálogo
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-7 space-y-4">
            <div class="rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden">
                <div class="aspect-[4/3] bg-gray-100">
                    @if($mainUrl)
                        <img src="{{ $mainUrl }}" alt="{{ $name }}" class="h-full w-full object-cover">
                    @else
                        <div class="h-full w-full flex items-center justify-center text-gray-400">
                            <svg class="h-12 w-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                                <path d="M3 16l5-5 4 4 3-3 6 6"></path>
                                <path d="M14 8h.01"></path>
                            </svg>
                        </div>
                    @endif
                </div>
            </div>

            @if($images->count() > 1)
                <div class="grid grid-cols-4 sm:grid-cols-6 gap-3">
                    @foreach($images as $img)
                        @php $u = $imgUrlFrom($img); @endphp
                        <a href="{{ $u }}" target="_blank"
                           class="block h-20 w-20 rounded-xl overflow-hidden ring-1 ring-gray-200 bg-gray-100 hover:ring-gray-400">
                            @if($u)
                                <img src="{{ $u }}" alt="" class="h-full w-full object-cover">
                            @endif
                        </a>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500">Clique numa foto para abrir maior.</p>
            @endif
        </div>

        <div class="lg:col-span-5 space-y-6">
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900">
                    {{ $name }}
                </h1>

                <div class="mt-3 flex flex-wrap items-center gap-3">
                    @if($priceText)
                        <span class="inline-flex items-center rounded-xl bg-gray-900 px-4 py-2 text-sm font-semibold text-white">
                            {{ $priceText }}
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-xl bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700">
                            Sob consulta
                        </span>
                    @endif

                    @if($categoryName)
                        <span class="inline-flex items-center rounded-xl bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700">
                            {{ $categoryName }}
                        </span>
                    @endif

                    @if(isset($machine->negotiable) && $machine->negotiable)
                        <span class="inline-flex items-center rounded-xl bg-green-100 px-4 py-2 text-sm font-semibold text-green-700">
                            Negociável
                        </span>
                    @endif
                </div>

                @if(!empty($machine->brand) || !empty($machine->model))
                    <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                        @if(!empty($machine->brand))
                            <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                                <div class="text-xs text-gray-500">Marca</div>
                                <div class="font-semibold text-gray-900 mt-1">{{ $machine->brand }}</div>
                            </div>
                        @endif
                        @if(!empty($machine->model))
                            <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                                <div class="text-xs text-gray-500">Modelo</div>
                                <div class="font-semibold text-gray-900 mt-1">{{ $machine->model }}</div>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="mt-6">
                    <div class="text-sm font-semibold text-gray-900">Descrição</div>
                    @if(!empty($machine->description))
                        <div class="mt-2 text-sm leading-relaxed text-gray-700 whitespace-pre-line">
                            {{ $machine->description }}
                        </div>
                    @else
                        <div class="mt-2 text-sm text-gray-500">Sem descrição.</div>
                    @endif
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="text-sm font-semibold text-gray-900">Contactar</div>
                <p class="mt-2 text-sm text-gray-600">
                    Para confirmar preço, disponibilidade ou envio, usa a página de contacto.
                </p>
                <a href="{{ route('site.contact') }}"
                   class="mt-4 inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-gray-800">
                    Ir para contacto
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
