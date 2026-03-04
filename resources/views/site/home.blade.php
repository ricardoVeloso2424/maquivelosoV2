@extends('layouts.site')

@section('content')
@php
    $money = function ($value) {
        if ($value === null || $value === '') return null;
        return number_format((float) $value, 0, ',', '.') . ' €';
    };

    $featuredMachines = $featuredMachines ?? collect();
@endphp

<section class="bg-white">
    <div class="max-w-7xl mx-auto px-6 py-20 text-center">
        <h1 class="text-4xl font-bold mb-6">
            Máquinas de costura com confiança
        </h1>

        <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-10">
            Reparação, manutenção e venda de máquinas de costura.
            Experiência, qualidade e acompanhamento personalizado.
        </p>

        <a href="{{ route('site.catalog') }}"
           class="inline-block px-6 py-3 bg-gray-900 text-white rounded-lg font-medium hover:bg-gray-800">
            Ver catálogo
        </a>
    </div>
</section>

<section class="max-w-7xl mx-auto px-6 py-8 md:py-12">
    <div class="flex items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Máquinas em destaque</h2>
        </div>

        <a href="{{ route('site.catalog') }}" class="text-sm font-semibold text-gray-700 hover:text-gray-900">
            Ver catálogo completo
        </a>
    </div>

    @if($featuredMachines->isNotEmpty())
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($featuredMachines as $machine)
                @php
                    $imageUrl = $machine->firstImage?->public_url;
                    $priceText = $money($machine->price);
                @endphp

                <a href="{{ route('site.machine.show', $machine) }}"
                   class="group rounded-2xl border border-gray-100 bg-white shadow-sm hover:shadow-md transition overflow-hidden">
                    <div class="aspect-[4/3] bg-gray-100 overflow-hidden">
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $machine->name }}" class="h-full w-full object-cover group-hover:scale-[1.02] transition">
                        @else
                            <div class="h-full w-full flex items-center justify-center text-gray-400 text-sm">
                                Sem imagem
                            </div>
                        @endif
                    </div>

                    <div class="p-5">
                        <div class="font-semibold text-gray-900 truncate">{{ $machine->name }}</div>

                        <div class="mt-1 text-sm">
                            @if($priceText)
                                <span class="font-semibold text-gray-900">{{ $priceText }}</span>
                            @elseif($machine->negotiable)
                                <span class="inline-flex items-center rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">
                                    Negociável
                                </span>
                            @else
                                <span class="text-gray-500">Sob consulta</span>
                            @endif
                        </div>

                        <div class="mt-4 text-xs text-gray-500">Ver detalhes</div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="mt-6 rounded-2xl border border-gray-100 bg-white p-8 text-center text-sm text-gray-600">
            Ainda não há máquinas em destaque.
        </div>
    @endif
</section>

<section class="max-w-7xl mx-auto px-6 py-16 grid grid-cols-1 md:grid-cols-3 gap-8">
    <div class="bg-white border rounded-xl p-6 text-center">
        <h3 class="font-semibold text-lg mb-2">Reparações</h3>
        <p class="text-sm text-gray-600">
            Diagnóstico e reparação de máquinas domésticas e industriais.
        </p>
    </div>

    <div class="bg-white border rounded-xl p-6 text-center">
        <h3 class="font-semibold text-lg mb-2">Venda</h3>
        <p class="text-sm text-gray-600">
            Máquinas revistas, prontas a trabalhar e com garantia.
        </p>
    </div>

    <div class="bg-white border rounded-xl p-6 text-center">
        <h3 class="font-semibold text-lg mb-2">Manutenção</h3>
        <p class="text-sm text-gray-600">
            Limpeza, afinação e prolongamento da vida útil da máquina.
        </p>
    </div>
</section>
@endsection
