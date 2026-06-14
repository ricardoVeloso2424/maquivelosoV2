@extends('layouts.site')

@section('content')
@php
    $featuredMachines = $featuredMachines ?? collect();
@endphp

<section class="bg-slate-950 text-white">
    <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:px-8">
        <p class="inline-flex rounded-full border border-white/20 px-3 py-1 text-xs font-medium uppercase tracking-wide text-slate-200">
            Loja especializada em máquinas de costura
        </p>

        <h1 class="mt-6 max-w-3xl text-4xl font-bold leading-tight tracking-tight sm:text-5xl">
            Máquinas prontas a trabalhar para acelerar o seu negócio.
        </h1>

        <p class="mt-5 max-w-2xl text-base leading-relaxed text-slate-200 sm:text-lg">
            Equipamentos revistos, suporte técnico contínuo e orientação para uma compra segura.
        </p>

        <div class="mt-8 flex flex-wrap items-center gap-4">
            <a href="{{ route('site.catalog') }}"
               class="inline-flex items-center justify-center rounded-xl bg-white px-6 py-3 text-sm font-semibold text-slate-900 transition hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950">
                Ver catálogo
            </a>
            <a href="{{ route('site.contact') }}"
               class="rounded text-sm font-medium text-slate-200 underline-offset-4 transition hover:text-white hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950">
                Falar com especialista
            </a>
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/80">
        <div class="grid grid-cols-1 divide-y divide-slate-100 sm:grid-cols-3 sm:divide-x sm:divide-y-0">
            <div class="p-5 sm:p-6">
                <h2 class="text-sm font-semibold text-slate-900">Revisão e assistência</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Máquinas verificadas e apoio técnico após a compra.</p>
            </div>
            <div class="p-5 sm:p-6">
                <h2 class="text-sm font-semibold text-slate-900">Garantia de confiança</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Processo transparente com validação de estado e suporte comercial.</p>
            </div>
            <div class="p-5 sm:p-6">
                <h2 class="text-sm font-semibold text-slate-900">Entrega ou levantamento</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Opções ajustadas à localização e urgência da sua empresa.</p>
            </div>
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 pb-10 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Máquinas em destaque</h2>
            <p class="mt-1 text-sm text-slate-600">Seleção atual de modelos disponíveis.</p>
        </div>

        <a href="{{ route('site.catalog') }}"
           class="rounded text-sm font-medium text-slate-700 transition hover:text-slate-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2">
            Ver catálogo completo
        </a>
    </div>

    @if($featuredMachines->isNotEmpty())
        <div class="mt-7 grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach($featuredMachines as $machine)
                @php
                    $imageUrl = $machine->main_image?->thumb_url;
                    $priceText = $machine->price_formatted;
                    $state = $machine->priceState();
                @endphp

                <a href="{{ route('site.machine.show', $machine) }}"
                   class="group overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2">
                    <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $machine->name }}" width="800" height="600" loading="lazy" decoding="async" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]">
                        @else
                            <div class="flex h-full w-full items-center justify-center text-sm text-slate-500">Sem imagem</div>
                        @endif

                        @if($machine->negotiable)
                            <span class="absolute left-3 top-3 inline-flex items-center rounded-full bg-emerald-500 px-2.5 py-1 text-xs font-semibold text-white">
                                Negociável
                            </span>
                        @endif
                    </div>

                    <div class="p-5">
                        <h3 class="line-clamp-1 text-lg font-semibold text-slate-900">{{ $machine->name }}</h3>

                        <div class="mt-2 min-h-[2rem]">
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
            @endforeach
        </div>
    @else
        <div class="mt-7 rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
            <p class="text-sm text-slate-600">Ainda não há máquinas em destaque disponíveis.</p>
            <a href="{{ route('site.catalog') }}"
               class="mt-4 inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2">
                Ver catálogo
            </a>
        </div>
    @endif
</section>
@endsection
