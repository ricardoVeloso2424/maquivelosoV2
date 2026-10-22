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
<section class="bg-white" aria-labelledby="hero-title">
    <div class="mx-auto max-w-7xl px-4 pb-14 pt-12 sm:px-6 sm:pt-16 lg:px-8 lg:pb-20 lg:pt-24">
        <div class="grid gap-8 lg:grid-cols-12 lg:items-end lg:gap-12">
            <h1 id="hero-title" class="text-[2.5rem] font-semibold leading-[1.05] tracking-[-0.03em] text-navy-950 sm:text-6xl lg:col-span-7 lg:text-[4.25rem]">
                Máquinas de costura revistas e com assistência.
            </h1>

            <div class="lg:col-span-5 lg:pb-2">
                <p class="max-w-md text-lg leading-relaxed text-slate-600">
                    A {{ $businessName }} vende máquinas de costura e acessórios e ajuda a escolher a solução certa para o seu trabalho.
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <x-ui.button :href="route('site.catalog')" variant="navy" shape="rounded" size="lg">Ver catálogo</x-ui.button>
                    <x-ui.button :href="route('site.contact')" variant="navy-outline" shape="rounded" size="lg">Pedir aconselhamento</x-ui.button>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ======================= SERVICES ======================= --}}
<section class="border-y border-slate-200 bg-slate-50" aria-labelledby="servicos">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 lg:grid-cols-4 lg:gap-0 lg:px-8 lg:py-16">
        <h2 id="servicos" class="max-w-xs text-2xl font-semibold leading-tight tracking-tight text-navy-950 lg:pr-10">
            Antes, durante e depois da compra.
        </h2>

        @foreach($services as $service)
            <div class="border-t border-slate-200 pt-6 lg:border-l lg:border-t-0 lg:px-8 lg:pt-0">
                <svg class="h-6 w-6 text-navy-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $service['icon'] !!}</svg>
                <h3 class="mt-4 font-semibold text-navy-950">{{ $service['title'] }}</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $service['text'] }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- ======================= PRODUCTS ======================= --}}
<section class="bg-white" aria-labelledby="produtos">
    <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-24">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 id="produtos" class="text-3xl font-semibold tracking-tight text-navy-950 sm:text-4xl">
                    {{ $featuredMachines->isNotEmpty() ? 'Máquinas em destaque' : 'Do nosso catálogo' }}
                </h2>
                <p class="mt-3 max-w-xl leading-relaxed text-slate-600">
                    {{ $featuredMachines->isNotEmpty() ? 'Uma seleção dos modelos disponíveis neste momento.' : 'Alguns dos produtos disponíveis em cada categoria.' }}
                </p>
            </div>
            @if($products->isNotEmpty())
                <a href="{{ route('site.catalog') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-navy-700 underline-offset-4 hover:underline">Ver o catálogo completo</a>
            @endif
        </div>

        @if($products->isNotEmpty())
            <ul class="mt-10 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 lg:gap-6">
                @foreach($products as $machine)
                    <li><x-machine.card :machine="$machine" /></li>
                @endforeach
            </ul>
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
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-20 sm:px-6 lg:grid-cols-12 lg:gap-12 lg:px-8 lg:py-24">
        <h2 id="sobre" class="text-3xl font-semibold leading-tight tracking-tight sm:text-4xl lg:col-span-6">
            Conhecemos as máquinas e quem trabalha com elas.
        </h2>
        <div class="lg:col-span-5 lg:col-start-8">
            <p class="text-lg leading-relaxed text-navy-100">
                Trabalhamos apenas com máquinas de costura e acessórios. É esse foco que nos permite aconselhar com conhecimento, rever cada máquina antes de sair e continuar disponíveis quando precisa de assistência.
            </p>
            <dl class="mt-10 grid gap-8 border-t border-white/15 pt-8 sm:grid-cols-2">
                <div>
                    <dt class="font-semibold">Especialização</dt>
                    <dd class="mt-2 text-sm leading-relaxed text-navy-100">Só máquinas de costura e acessórios: é o que conhecemos a fundo.</dd>
                </div>
                <div>
                    <dt class="font-semibold">Conselho adequado</dt>
                    <dd class="mt-2 text-sm leading-relaxed text-navy-100">Indicamos a solução certa para o seu trabalho, não a mais cara.</dd>
                </div>
            </dl>
        </div>
    </div>
</section>

{{-- ======================= FINAL CTA ======================= --}}
<section class="bg-white" aria-labelledby="contacto-cta">
    <div class="mx-auto flex max-w-7xl flex-col gap-8 px-4 py-20 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
        <div class="max-w-2xl">
            <h2 id="contacto-cta" class="text-3xl font-semibold tracking-tight text-navy-950 sm:text-4xl">Não encontra o que procura?</h2>
            <p class="mt-4 text-lg leading-relaxed text-slate-600">Diga-nos o que precisa e ajudamos a encontrar a máquina ou o acessório certo.</p>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row">
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
</section>
@endsection
