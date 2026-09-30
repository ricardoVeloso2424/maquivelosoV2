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
        <svg viewBox="0 0 200 200" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="100" cy="100" r="90" stroke-width="1.5" stroke-dasharray="5 9"></circle>
            <circle cx="100" cy="100" r="62" stroke-width="2"></circle>
            <path d="M100 84V38" stroke-width="2"></path>
            <path d="M100 116V162" stroke-width="2"></path>
            <path d="M113.9 92 153.7 69" stroke-width="2"></path>
            <path d="M86.1 92 46.3 69" stroke-width="2"></path>
            <path d="M86.1 108 46.3 131" stroke-width="2"></path>
            <path d="M113.9 108 153.7 131" stroke-width="2"></path>
            <circle cx="100" cy="100" r="11" stroke-width="2" class="text-brand-400"></circle>
        </svg>
    </div>
    <div class="pointer-events-none absolute -left-32 -top-32 h-96 w-96 rounded-full bg-brand-500/10 blur-3xl" aria-hidden="true"></div>

    <div class="relative mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28">
        <div class="max-w-3xl motion-safe:animate-fade-up">


            <h1 class="mt-7 font-serif text-4xl font-semibold leading-[1.08] tracking-tight sm:text-5xl lg:text-6xl">
                A máquina certa para fazer o seu negócio avançar.
            </h1>

            <p class="mt-6 max-w-2xl text-lg leading-relaxed text-stone-300">
                Equipamentos revistos, suporte técnico e orientação para uma compra segura.
            </p>

            <div class="mt-9 flex flex-col gap-3 sm:flex-row sm:items-center">
                <x-ui.button :href="route('site.catalog')" variant="light" size="lg">
                    Ver catálogo
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M5 12h14"></path>
                        <path d="M13 5l7 7-7 7"></path>
                    </svg>
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
        ['title' => 'Aconselhamento pré-venda', 'text' => 'Ajudamos a escolher a máquina mais adequada às suas necessidades, orçamento e tipo de utilização.','icon' => 'chat'],
        ['title' => 'Revisão e assistência', 'text' => 'Máquinas revistas e apoio técnico após a compra.', 'icon' => 'wrench'],
        ['title' => 'Entrega ou levantamento', 'text' => 'Opções ajustadas à localização e urgência da sua empresa.', 'icon' => 'truck'],
        ];
        @endphp

        @foreach($values as $i => $value)
        <div data-reveal data-reveal-delay="{{ $i + 1 }}" class="group relative overflow-hidden rounded-3xl bg-white p-7 shadow-card ring-1 ring-stone-200/70 transition duration-300 hover:-translate-y-1 hover:shadow-card-hover">
            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-700 ring-1 ring-brand-100">
                @if($value['icon'] === 'chat')
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"></path>
                    <path d="M8 9h8"></path>
                    <path d="M8 13h5"></path>
                </svg>
                @elseif($value['icon'] === 'shield')
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M12 3l8 3v5c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V6l8-3z"></path>
                    <path d="M9 12l2 2 4-4"></path>
                </svg>
                @else
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M3 6h11v9H3z"></path>
                    <path d="M14 9h4l3 3v3h-7z"></path>
                    <circle cx="7" cy="18" r="1.6"></circle>
                    <circle cx="17" cy="18" r="1.6"></circle>
                </svg>
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
                ['t' => 'Aconselhamento especializado', 'd' => 'Apoio profissional para encontrar a solução mais adequada.'],
                ];
                @endphp
                @foreach($reasons as $reason)
                <li class="flex gap-4">
                    <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-stone-900 text-brand-400">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true">
                            <path d="M20 6L9 17l-5-5"></path>
                        </svg>
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
                    <p class="mt-6 font-serif text-2xl font-medium leading-snug">
                        “Conhecemos as máquinas, mas também entendemos as necessidades de quem trabalha com elas.”
                    </p>
                    <p class="mt-4 text-sm leading-relaxed text-stone-400">
                        Soluções ajustadas à realidade de cada empresa, com proximidade e conhecimento técnico.
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


@endsection