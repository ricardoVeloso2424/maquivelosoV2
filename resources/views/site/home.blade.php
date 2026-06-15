@extends('layouts.site')

@section('content')
@php
    $featuredMachines = $featuredMachines ?? collect();
    $businessName = $siteSettings['business_name'] ?? 'MaquiVeloso';
@endphp

{{-- ============================ HERO ============================ --}}
<section class="relative overflow-hidden bg-stone-950 text-white">
    <div class="pattern-dots-light pointer-events-none absolute inset-0 opacity-50"></div>
    <div class="pointer-events-none absolute -right-24 top-1/2 hidden h-[34rem] w-[34rem] -translate-y-1/2 text-brand-500/20 motion-safe:animate-float lg:block" aria-hidden="true">
        <svg viewBox="0 0 200 200" fill="none" stroke="currentColor">
            <circle cx="100" cy="100" r="92" stroke-width="1.5" stroke-dasharray="6 8"></circle>
            <circle cx="100" cy="100" r="68" stroke-width="1.5" stroke-dasharray="6 8"></circle>
            <circle cx="100" cy="100" r="44" stroke-width="1.5" stroke-dasharray="6 8"></circle>
            <circle cx="100" cy="100" r="10" stroke-width="2" class="text-brand-400"></circle>
        </svg>
    </div>
    <div class="pointer-events-none absolute -left-32 -top-32 h-96 w-96 rounded-full bg-brand-500/10 blur-3xl" aria-hidden="true"></div>

    <div class="relative mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28">
        <div class="max-w-3xl motion-safe:animate-fade-up">
            <span class="inline-flex items-center gap-2.5 rounded-full border border-white/15 bg-white/5 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.2em] text-brand-300 backdrop-blur">
                <span class="h-px w-6 stitch-line"></span>
                Loja especializada em máquinas de costura
            </span>

            <h1 class="mt-7 font-serif text-4xl font-semibold leading-[1.08] tracking-tight sm:text-5xl lg:text-6xl">
                Máquinas prontas a trabalhar para acelerar o seu negócio.
            </h1>

            <p class="mt-6 max-w-2xl text-lg leading-relaxed text-stone-300">
                Equipamentos revistos, suporte técnico contínuo e orientação para uma compra segura.
            </p>

            <div class="mt-9 flex flex-col gap-3 sm:flex-row sm:items-center">
                <x-ui.button :href="route('site.catalog')" variant="light" size="lg">
                    Ver catálogo
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14"></path><path d="M13 5l7 7-7 7"></path></svg>
                </x-ui.button>
                <x-ui.button :href="route('site.contact')" variant="on-dark" size="lg">
                    Falar com especialista
                </x-ui.button>
            </div>
        </div>
    </div>

    <div class="relative h-px w-full stitch-line text-white/15"></div>
</section>

{{-- ======================= VALUE / SERVICES ======================= --}}
<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
    <div class="grid gap-6 md:grid-cols-3">
        @php
            $values = [
                ['title' => 'Revisão e assistência', 'text' => 'Máquinas verificadas e apoio técnico após a compra.', 'icon' => 'wrench'],
                ['title' => 'Garantia de confiança', 'text' => 'Processo transparente com validação de estado e suporte comercial.', 'icon' => 'shield'],
                ['title' => 'Entrega ou levantamento', 'text' => 'Opções ajustadas à localização e urgência da sua empresa.', 'icon' => 'truck'],
            ];
        @endphp

        @foreach($values as $i => $value)
            <div data-reveal data-reveal-delay="{{ $i + 1 }}" class="group relative overflow-hidden rounded-3xl bg-white p-7 shadow-card ring-1 ring-stone-200/70 transition duration-300 hover:-translate-y-1 hover:shadow-card-hover">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-700 ring-1 ring-brand-100">
                    @if($value['icon'] === 'wrench')
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.2L3 18l3 3 6.5-6.3a4 4 0 0 0 5.2-5.4l-2.6 2.6-2.2-2.2 2.6-2.6z"></path></svg>
                    @elseif($value['icon'] === 'shield')
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 3l8 3v5c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V6l8-3z"></path><path d="M9 12l2 2 4-4"></path></svg>
                    @else
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 6h11v9H3z"></path><path d="M14 9h4l3 3v3h-7z"></path><circle cx="7" cy="18" r="1.6"></circle><circle cx="17" cy="18" r="1.6"></circle></svg>
                    @endif
                </span>
                <h3 class="mt-5 text-lg font-semibold text-stone-900">{{ $value['title'] }}</h3>
                <p class="mt-2 text-sm leading-relaxed text-stone-600">{{ $value['text'] }}</p>
                <span class="pointer-events-none absolute -right-6 -bottom-6 h-20 w-20 rounded-full bg-brand-50 opacity-0 transition duration-300 group-hover:opacity-100"></span>
            </div>
        @endforeach
    </div>
</section>

{{-- ======================= FEATURED MACHINES ======================= --}}
<section class="mx-auto max-w-7xl px-4 pb-4 sm:px-6 lg:px-8">
    <div data-reveal class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <x-ui.section-heading eyebrow="Catálogo" title="Máquinas em destaque">
            Seleção atual de modelos disponíveis.
        </x-ui.section-heading>
        <a href="{{ route('site.catalog') }}" class="nav-link hidden text-sm sm:inline-block">Ver catálogo completo →</a>
    </div>

    @if($featuredMachines->isNotEmpty())
        <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
            @foreach($featuredMachines as $i => $machine)
                <div data-reveal data-reveal-delay="{{ ($i % 3) + 1 }}">
                    <x-machine.card :machine="$machine" />
                </div>
            @endforeach
        </div>

        <div class="mt-10 flex justify-center sm:hidden">
            <x-ui.button :href="route('site.catalog')" variant="outline" size="md">Ver catálogo completo</x-ui.button>
        </div>
    @else
        <div class="mt-10" data-reveal>
            <x-ui.empty-state title="Ainda não há máquinas em destaque">
                Assim que houver modelos em destaque disponíveis, aparecem aqui.
                <x-slot:action>
                    <x-ui.button :href="route('site.catalog')" variant="dark" size="md">Ver catálogo</x-ui.button>
                </x-slot:action>
            </x-ui.empty-state>
        </div>
    @endif
</section>

{{-- ======================= WHY CHOOSE US ======================= --}}
<section class="mx-auto mt-20 max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="grid items-center gap-10 lg:grid-cols-2">
        <div data-reveal>
            <x-ui.section-heading eyebrow="Experiência" title="Porquê escolher a {{ $businessName }}">
                Especialização dedicada a máquinas de costura, com a tranquilidade de um acompanhamento próximo do início ao fim.
            </x-ui.section-heading>

            <ul class="mt-8 space-y-5">
                @php
                    $reasons = [
                        ['t' => 'Foco exclusivo em costura', 'd' => 'Conhecemos a fundo máquinas domésticas e industriais.'],
                        ['t' => 'Revisão antes da venda', 'd' => 'Cada equipamento é verificado para chegar pronto a trabalhar.'],
                        ['t' => 'Apoio que continua', 'd' => 'Suporte técnico contínuo depois da compra.'],
                        ['t' => 'Aconselhamento honesto', 'd' => 'Orientação clara para uma escolha segura.'],
                    ];
                @endphp
                @foreach($reasons as $reason)
                    <li class="flex gap-4">
                        <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-stone-900 text-brand-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M20 6L9 17l-5-5"></path></svg>
                        </span>
                        <div>
                            <h3 class="font-semibold text-stone-900">{{ $reason['t'] }}</h3>
                            <p class="mt-1 text-sm leading-relaxed text-stone-600">{{ $reason['d'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <div data-reveal data-reveal-delay="2" class="relative">
            <div class="relative overflow-hidden rounded-4xl bg-stone-900 p-10 text-white shadow-card">
                <div class="pattern-dots-light pointer-events-none absolute inset-0 opacity-40"></div>
                <div class="pointer-events-none absolute -right-16 -top-16 h-56 w-56 rounded-full bg-brand-500/15 blur-2xl"></div>
                <div class="relative">
                    <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/5 text-brand-400 ring-1 ring-white/10">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                            <circle cx="12" cy="12" r="3.2"></circle>
                            <path d="M12 2v6"></path><path d="M12 16v6"></path><path d="M4.5 7l3 2"></path><path d="M16.5 15l3 2"></path><path d="M19.5 7l-3 2"></path><path d="M7.5 15l-3 2"></path>
                        </svg>
                    </span>
                    <p class="mt-6 font-serif text-2xl font-medium leading-snug">
                        “Tratamos cada máquina como se fosse para o nosso próprio atelier.”
                    </p>
                    <p class="mt-4 text-sm leading-relaxed text-stone-400">
                        Rigor técnico e proximidade humana — a combinação que dá confiança a quem compra e repara connosco.
                    </p>
                    <div class="mt-8 h-px w-full stitch-line text-white/15"></div>
                    <div class="mt-6">
                        <x-ui.button :href="route('site.contact')" variant="light" size="md">Falar connosco</x-ui.button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ======================= FINAL CTA ======================= --}}
<section class="mx-auto mt-20 max-w-7xl px-4 sm:px-6 lg:px-8">
    <div data-reveal class="relative overflow-hidden rounded-4xl bg-gradient-to-br from-brand-600 to-brand-800 px-6 py-14 text-center shadow-card sm:px-12 sm:py-16">
        <div class="pattern-dots-light pointer-events-none absolute inset-0 opacity-30"></div>
        <div class="relative mx-auto max-w-2xl">
            <h2 class="font-serif text-3xl font-semibold tracking-tight text-white sm:text-4xl">
                Precisa de ajuda a escolher a máquina certa?
            </h2>
            <p class="mt-4 text-base leading-relaxed text-brand-50">
                Diga-nos o que procura e ajudamos a encontrar a solução adequada, com revisão e apoio incluídos.
            </p>
            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                <x-ui.button :href="route('site.contact')" variant="light" size="lg">Pedir informação</x-ui.button>
                <x-ui.button :href="route('site.catalog')" variant="on-dark" size="lg">Explorar catálogo</x-ui.button>
            </div>
        </div>
    </div>
</section>
@endsection
