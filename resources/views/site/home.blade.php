@extends('layouts.site')

@section('content')
@php
    $featuredMachines = $featuredMachines ?? collect();
    $catalogPicks = $catalogPicks ?? collect();
    $categories = $categories ?? collect();
    $businessName = $siteSettings['business_name'] ?? 'MaquiVeloso';

    $products = $featuredMachines->isNotEmpty() ? $featuredMachines : $catalogPicks;

    $whatsapp = preg_replace('/\D+/', '', (string) ($siteSettings['contact_whatsapp'] ?? ''));
    $whatsapp = is_string($whatsapp) ? $whatsapp : '';

    $services = [
        [
            'title' => 'Aconselhamento pré-venda',
            'text' => 'Ajudamos a escolher a máquina adequada ao tipo de trabalho, ao volume de produção e ao orçamento.',
            'icon' => '<path d="M21 15a2 2 0 0 1-2 2H8l-4 4V5a2 2 0 0 1 2-2h13a2 2 0 0 1 2 2z"/><path d="M8 9h8M8 13h5"/>',
        ],
        [
            'title' => 'Revisão e assistência',
            'text' => 'As máquinas são revistas antes da venda e continuamos a dar apoio técnico depois da compra.',
            'icon' => '<path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 0 0 5.4-5.4l-2.5 2.5-2.4-.6-.6-2.4z"/>',
        ],
        [
            'title' => 'Entrega ou levantamento',
            'text' => 'Entregamos nas instalações do cliente ou pode levantar a máquina nas nossas instalações.',
            'icon' => '<path d="M3 7h11v9H3zM14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="1.6"/><circle cx="17" cy="18" r="1.6"/>',
        ],
    ];
@endphp

{{-- ============================ HERO ============================ --}}
<section class="relative isolate overflow-hidden bg-navy-950" aria-labelledby="hero-title">
    <img src="{{ asset('images/banners/home-banner.jpg') }}"
         alt=""
         width="2062" height="763" fetchpriority="high" decoding="async"
         class="absolute inset-0 -z-10 h-full w-full object-cover">
    <div class="absolute inset-0 -z-10 bg-gradient-to-r from-navy-950/90 via-navy-950/70 to-navy-950/40" aria-hidden="true"></div>

    <div class="mx-auto max-w-7xl px-4 pb-14 pt-12 sm:px-6 sm:pt-16 lg:px-8 lg:pb-20 lg:pt-24">
        <div class="grid gap-8 lg:grid-cols-12 lg:items-end lg:gap-12">
            <h1 id="hero-title" class="text-[2.5rem] font-semibold leading-[1.05] tracking-[-0.03em] text-white sm:text-6xl lg:col-span-7 lg:text-[4.25rem]">
                Máquinas de costura revistas e com assistência.
            </h1>

            <div class="lg:col-span-5 lg:pb-2">
                <div class="relative max-w-md">
                    <div class="pointer-events-none absolute -inset-x-10 -inset-y-8 -z-10 rounded-[3rem] bg-navy-950/60 blur-xl" aria-hidden="true"></div>
                    <p class="text-lg leading-relaxed text-white [text-shadow:0_1px_10px_rgba(10,22,40,0.55)]">
                        A {{ $businessName }} vende máquinas de costura e acessórios e ajuda a escolher a solução certa para o seu trabalho.
                    </p>
                </div>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <x-ui.button :href="route('site.catalog')" variant="navy-on-dark" shape="rounded" size="lg">Ver catálogo</x-ui.button>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ======================= SERVICES ======================= --}}
<section class="bg-[#d6e0ea]" aria-label="Serviços">
    <div class="mx-auto grid max-w-7xl gap-5 px-4 py-14 sm:px-6 lg:grid-cols-3 lg:gap-6 lg:px-8 lg:py-16">
        @foreach($services as $service)
            <div class="rounded-xl border border-navy-200 bg-white p-6">
                <svg class="h-6 w-6 text-navy-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $service['icon'] !!}</svg>
                <h3 class="mt-4 font-semibold text-navy-950">{{ $service['title'] }}</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $service['text'] }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- ======================= PRODUCTS ======================= --}}
<section class="bg-[#e3e7ec]" aria-labelledby="produtos">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
        <div class="flex flex-wrap items-baseline gap-x-6 gap-y-2">
            <h2 id="produtos" class="text-3xl font-semibold tracking-tight text-navy-950 sm:text-4xl">
                {{ $featuredMachines->isNotEmpty() ? 'Máquinas em destaque' : 'Do nosso catálogo' }}
            </h2>
            @if($products->isNotEmpty())
                <a href="{{ route('site.catalog') }}" class="group hidden items-center gap-1.5 text-sm font-medium text-slate-500 underline-offset-4 transition-colors hover:text-navy-900 hover:underline sm:inline-flex">
                    Ver o catálogo completo
                    <svg class="h-3.5 w-3.5 transition-transform motion-safe:group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"></path><path d="M13 6l6 6-6 6"></path></svg>
                </a>
            @endif
        </div>

        @if($products->isNotEmpty())
            <ul class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3 lg:gap-x-7 lg:gap-y-8">
                @foreach($products as $machine)
                    <li><x-machine.card :machine="$machine" /></li>
                @endforeach
            </ul>

            <x-ui.button :href="route('site.catalog')" variant="navy-outline" shape="rounded" size="md" class="mt-8 w-full sm:hidden">Ver o catálogo completo</x-ui.button>
        @else
            <div class="mt-10 rounded-xl border border-dashed border-slate-300 px-6 py-14 text-center">
                <p class="font-semibold text-navy-950">Ainda não há produtos publicados.</p>
                <p class="mt-2 text-sm text-slate-600">Fale connosco e diga-nos o que procura.</p>
                <x-ui.button :href="route('site.contact')" variant="navy" shape="rounded" size="md" class="mt-6">Pedir aconselhamento</x-ui.button>
            </div>
        @endif
    </div>
</section>

{{-- ======================= ABOUT ======================= --}}
<section class="bg-navy-900 text-white" aria-labelledby="sobre">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
        <div class="grid gap-5 lg:grid-cols-2 lg:items-start lg:gap-10">
            <h2 id="sobre" class="max-w-md text-2xl font-semibold leading-tight tracking-tight sm:text-3xl">
                Conhecemos as máquinas e quem trabalha com elas.
            </h2>
            <p class="max-w-xl leading-relaxed text-navy-100 sm:text-lg">
                Trabalhamos apenas com máquinas de costura e acessórios. É esse foco que nos permite aconselhar com conhecimento, rever cada máquina antes de sair e continuar disponíveis quando precisa de assistência.
            </p>
        </div>

        <dl class="mt-10 grid gap-4 sm:grid-cols-2 lg:gap-10">
            <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5 sm:p-6">
                <dt class="font-semibold">Especialização</dt>
                <dd class="mt-1.5 text-sm leading-relaxed text-navy-100 sm:text-base">Só máquinas de costura e acessórios: é o que conhecemos a fundo.</dd>
            </div>
            <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5 sm:p-6">
                <dt class="font-semibold">Conselho adequado</dt>
                <dd class="mt-1.5 text-sm leading-relaxed text-navy-100 sm:text-base">Indicamos a solução certa para o seu trabalho, não a mais cara.</dd>
            </div>
        </dl>
    </div>
</section>

{{-- ======================= FINAL CTA ======================= --}}
<section class="bg-white" aria-labelledby="contacto-cta">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between lg:gap-12">
            <div class="max-w-2xl">
                <h2 id="contacto-cta" class="text-2xl font-semibold tracking-tight text-navy-950 sm:text-3xl">Não encontra o que procura?</h2>
                <p class="mt-2 leading-relaxed text-slate-600 sm:text-lg">Diga-nos o que precisa e ajudamos a encontrar a máquina ou o acessório certo.</p>
            </div>
            <div class="flex shrink-0 flex-col gap-3 sm:flex-row">
                <x-ui.button :href="route('site.contact')" variant="navy" shape="rounded" size="lg">Pedir aconselhamento</x-ui.button>
                @if($whatsapp !== '')
                    <x-ui.button href="https://wa.me/{{ $whatsapp }}?text={{ rawurlencode('Olá! Gostaria de ajuda a encontrar uma máquina de costura.') }}"
                                 target="_blank" rel="noopener" variant="navy-outline" shape="rounded" size="lg">
                        <svg class="h-5 w-5 text-emerald-600" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.5 14.4c-.3-.1-1.7-.8-2-.9-.3-.1-.5-.1-.7.1-.2.3-.8.9-.9 1.1-.2.2-.3.2-.6.1-.3-.1-1.2-.5-2.3-1.4-.9-.8-1.4-1.7-1.6-2-.2-.3 0-.5.1-.6l.4-.5c.1-.2.2-.3.3-.5.1-.2 0-.4 0-.5l-.9-2.2c-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.4s1 2.8 1.2 3c.1.2 2 3.1 4.9 4.3.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.6-.1 1.7-.7 1.9-1.4.2-.7.2-1.2.2-1.4-.1-.1-.3-.2-.6-.3zM12 21.8c-1.8 0-3.5-.5-5-1.4l-.4-.2-3.7 1 1-3.6-.2-.4A9.8 9.8 0 0 1 2.2 12 9.8 9.8 0 0 1 12 2.2 9.8 9.8 0 0 1 21.8 12 9.8 9.8 0 0 1 12 21.8zM12 .2A11.8 11.8 0 0 0 1.8 17.9L.1 24l6.3-1.6A11.8 11.8 0 0 0 23.8 12 11.8 11.8 0 0 0 12 .2z"/></svg>
                        WhatsApp
                    </x-ui.button>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
