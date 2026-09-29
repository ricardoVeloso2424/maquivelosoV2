<!DOCTYPE html>
<html lang="pt" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0c0a09">
    <title>{{ $siteSettings['business_name'] ?? 'MaquiVeloso' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/branding/maquivelosoLogo.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex min-h-screen flex-col bg-stone-50 text-stone-800 antialiased">
    @php
        $businessName = $siteSettings['business_name'] ?? 'MaquiVeloso';
        $phone = trim((string) ($siteSettings['contact_phone'] ?? ($siteSettings['phone'] ?? '')));
        $email = trim((string) ($siteSettings['contact_email'] ?? ($siteSettings['email'] ?? '')));
        $location = trim((string) ($siteSettings['contact_address'] ?? ($siteSettings['location'] ?? '')));
        $hours = trim((string) ($siteSettings['contact_hours'] ?? ''));

        $phoneHref = preg_replace('/[^\d+]/', '', $phone);
        $phoneHref = is_string($phoneHref) ? $phoneHref : '';
        $locationOneLine = trim(preg_replace('/\s+/', ' ', $location));
    @endphp

    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[60] focus:rounded-lg focus:bg-stone-900 focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">
        Saltar para o conteúdo
    </a>

    <header x-data="{ open: false, scrolled: false }" @scroll.window="scrolled = window.scrollY > 8" class="sticky top-0 z-50">
        {{-- Utility bar (real settings only) --}}
        @if($phone !== '' || $email !== '' || $locationOneLine !== '')
            <div class="hidden bg-stone-950 text-stone-300 md:block">
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-6 py-2 text-xs lg:px-8">
                    <div class="flex items-center gap-6">
                        @if($phone !== '' && $phoneHref !== '')
                            <a href="tel:{{ $phoneHref }}" class="inline-flex items-center gap-2 transition hover:text-white">
                                <svg class="h-3.5 w-3.5 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.9.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                {{ $phone }}
                            </a>
                        @endif
                        @if($email !== '')
                            <a href="mailto:{{ $email }}" class="hidden items-center gap-2 transition hover:text-white lg:inline-flex">
                                <svg class="h-3.5 w-3.5 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m3 7 9 6 9-6"></path></svg>
                                {{ $email }}
                            </a>
                        @endif
                    </div>
                    @if($locationOneLine !== '')
                        <span class="inline-flex items-center gap-2 text-stone-400">
                            <svg class="h-3.5 w-3.5 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            {{ $locationOneLine }}
                        </span>
                    @endif
                </div>
            </div>
        @endif

        {{-- Main navigation --}}
        <div class="border-b border-stone-200/70 bg-stone-50/85 backdrop-blur transition-shadow duration-300" :class="scrolled ? 'shadow-sm' : ''">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8 lg:h-20">
                <a href="{{ route('site.home') }}" class="group inline-flex items-center gap-3 rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2">
                    <img src="{{ asset('images/branding/maquivelosoLogo.png') }}" alt="MaquiVeloso" class="h-10 w-10 object-contain">

                    <span class="flex flex-col leading-none">
                        <span class="font-serif text-lg font-semibold tracking-tight text-stone-900 sm:text-xl">{{ $businessName }}</span>
                        <span class="mt-0.5 text-[10px] font-semibold uppercase tracking-[0.22em] text-stone-400">Máquinas de costura</span>
                    </span>
                </a>

                <nav class="hidden items-center gap-9 text-sm md:flex" aria-label="Navegação principal">
                    <a href="{{ route('site.home') }}" class="nav-link" @if(request()->routeIs('site.home')) aria-current="page" @endif>Início</a>
                    <a href="{{ route('site.catalog') }}" class="nav-link" @if(request()->routeIs('site.catalog') || request()->routeIs('site.machine.show')) aria-current="page" @endif>Catálogo</a>
                    <a href="{{ route('site.contact') }}" class="nav-link" @if(request()->routeIs('site.contact')) aria-current="page" @endif>Contacto</a>
                </nav>

                <div class="hidden md:block">
                    <x-ui.button :href="route('site.contact')" variant="primary" size="md">
                        Pedir informação
                    </x-ui.button>
                </div>

                {{-- Mobile toggle --}}
                <button
                    type="button"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-stone-300 bg-white text-stone-800 transition hover:border-stone-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 md:hidden"
                    @click="open = !open"
                    :aria-expanded="open.toString()"
                    aria-controls="mobile-menu"
                    aria-label="Abrir menu de navegação"
                >
                    <svg x-show="!open" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 7h16"></path><path d="M4 12h16"></path><path d="M4 17h16"></path></svg>
                    <svg x-show="open" x-cloak class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 6l12 12"></path><path d="M18 6 6 18"></path></svg>
                </button>
            </div>

            {{-- Mobile menu --}}
            <div
                id="mobile-menu"
                x-show="open"
                x-cloak
                x-transition.origin.top
                class="border-t border-stone-200 bg-stone-50 md:hidden"
            >
                <nav class="mx-auto flex max-w-7xl flex-col gap-1 px-4 py-4 text-sm font-semibold" aria-label="Navegação mobile">
                    <a href="{{ route('site.home') }}" @click="open = false" class="rounded-xl px-4 py-3 transition {{ request()->routeIs('site.home') ? 'bg-stone-900 text-white' : 'text-stone-700 hover:bg-stone-100' }}">Início</a>
                    <a href="{{ route('site.catalog') }}" @click="open = false" class="rounded-xl px-4 py-3 transition {{ request()->routeIs('site.catalog') || request()->routeIs('site.machine.show') ? 'bg-stone-900 text-white' : 'text-stone-700 hover:bg-stone-100' }}">Catálogo</a>
                    <a href="{{ route('site.contact') }}" @click="open = false" class="rounded-xl px-4 py-3 transition {{ request()->routeIs('site.contact') ? 'bg-stone-900 text-white' : 'text-stone-700 hover:bg-stone-100' }}">Contacto</a>
                    <x-ui.button :href="route('site.contact')" variant="primary" size="md" class="mt-2 w-full">Pedir informação</x-ui.button>
                </nav>
            </div>
        </div>
    </header>

    <main id="main-content" class="flex-1 overflow-x-hidden">
        @yield('content')
    </main>

    <footer class="relative mt-24 overflow-hidden bg-stone-950 text-stone-300">
        <div class="h-1 w-full bg-gradient-to-r from-transparent via-brand-500/70 to-transparent"></div>
        <div class="pattern-dots-light pointer-events-none absolute inset-0 opacity-40"></div>

        <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-3">
                {{-- Brand --}}
                <div class="lg:pr-8">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/branding/maquivelosoLogo.png') }}" alt="MaquiVeloso" class="h-10 w-10 object-contain">

                        <span class="font-serif text-xl font-semibold text-white">{{ $businessName }}</span>
                    </div>
                    <p class="mt-4 max-w-sm text-sm leading-relaxed text-stone-400">
                        Venda, revisão e acompanhamento para máquinas de costura domésticas e industriais.
                    </p>

                </div>

                {{-- Contact (real settings) --}}
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-[0.18em] text-white">Contacto</h3>
                    <ul class="mt-5 space-y-3 text-sm text-stone-400">
                        @if($phone !== '' && $phoneHref !== '')
                            <li>
                                <a href="tel:{{ $phoneHref }}" class="inline-flex items-start gap-3 transition hover:text-white">
                                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.9.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                    <span>{{ $phone }}</span>
                                </a>
                            </li>
                        @endif
                        @if($email !== '')
                            <li>
                                <a href="mailto:{{ $email }}" class="inline-flex items-start gap-3 break-all transition hover:text-white">
                                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m3 7 9 6 9-6"></path></svg>
                                    <span>{{ $email }}</span>
                                </a>
                            </li>
                        @endif
                        @if($location !== '')
                            <li class="inline-flex items-start gap-3">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                <span class="whitespace-pre-line">{{ $location }}</span>
                            </li>
                        @endif
                        @if($hours !== '')
                            <li class="inline-flex items-start gap-3">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v6l4 2"></path></svg>
                                <span class="whitespace-pre-line">{{ $hours }}</span>
                            </li>
                        @endif
                        @if($phone === '' && $email === '' && $location === '' && $hours === '')
                            <li class="text-stone-500">Contactos disponíveis em breve.</li>
                        @endif
                    </ul>
                </div>

                {{-- Specialisation (qualitative, no invented facts) --}}
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-[0.18em] text-white">Especialização</h3>
                    <ul class="mt-5 space-y-3 text-sm text-stone-400">
                        <li class="flex items-start gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-brand-500"></span>Máquinas revistas antes da venda</li>
                        <li class="flex items-start gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-brand-500"></span>Apoio técnico após a compra</li>
                        <li class="flex items-start gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-brand-500"></span>Aconselhamento especializado</li>
                    </ul>
                </div>
            </div>


        </div>
    </footer>
</body>
</html>
