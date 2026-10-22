<!DOCTYPE html>
<html lang="pt" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#10203a">
    <title>{{ $siteSettings['business_name'] ?? 'MaquiVeloso' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/branding/maquivelosoLogo.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex min-h-screen flex-col bg-slate-50 text-slate-800 antialiased">
    @php
        $businessName = $siteSettings['business_name'] ?? 'MaquiVeloso';
        $phone = trim((string) ($siteSettings['contact_phone'] ?? ($siteSettings['phone'] ?? '')));
        $email = trim((string) ($siteSettings['contact_email'] ?? ($siteSettings['email'] ?? '')));
        $location = trim((string) ($siteSettings['contact_address'] ?? ($siteSettings['location'] ?? '')));
        $hours = trim((string) ($siteSettings['contact_hours'] ?? ''));

        $phoneHref = preg_replace('/[^\d+]/', '', $phone);
        $phoneHref = is_string($phoneHref) ? $phoneHref : '';

        $navItems = [
            ['label' => 'Início', 'route' => 'site.home', 'active' => request()->routeIs('site.home')],
            ['label' => 'Catálogo', 'route' => 'site.catalog', 'active' => request()->routeIs('site.catalog') || request()->routeIs('site.machine.show')],
            ['label' => 'Contacto', 'route' => 'site.contact', 'active' => request()->routeIs('site.contact')],
        ];
    @endphp

    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[60] focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-navy-950">
        Saltar para o conteúdo
    </a>

    <header x-data="{ open: false }" @keydown.escape.window="open = false" class="sticky top-0 z-50 border-b border-white/[0.08] bg-navy-900 text-white">
        <div class="mx-auto flex h-[4.5rem] max-w-7xl items-center justify-between gap-8 px-4 sm:px-6 lg:px-8">
            <a href="{{ route('site.home') }}" class="inline-flex shrink-0 items-center gap-3.5 rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/70 focus-visible:ring-offset-2 focus-visible:ring-offset-navy-900">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white">
                    <img src="{{ asset('images/branding/maquivelosoLogo.png') }}" alt="" class="h-11 w-11 rounded-full object-contain">
                </span>
                <span class="text-[1.1875rem] font-semibold tracking-[-0.01em]">{{ $businessName }}</span>
            </a>

            <div class="hidden h-full items-center gap-8 md:flex">
                <nav class="flex h-full items-center gap-7" aria-label="Navegação principal">
                    @foreach($navItems as $item)
                        <a href="{{ route($item['route']) }}"
                           @if($item['active']) aria-current="page" @endif
                           class="relative flex h-full items-center text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-white/70 {{ $item['active'] ? 'text-white after:absolute after:inset-x-0 after:bottom-0 after:h-0.5 after:rounded-full after:bg-white' : 'text-white/70 hover:text-white' }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                <span class="h-5 w-px bg-white/15" aria-hidden="true"></span>

                <div class="flex items-center gap-5">
                    @if($phone !== '' && $phoneHref !== '')
                        <a href="tel:{{ $phoneHref }}" class="hidden rounded text-[0.8125rem] tabular-nums text-white/55 transition-colors hover:text-white/90 lg:inline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/70 focus-visible:ring-offset-2 focus-visible:ring-offset-navy-900">{{ $phone }}</a>
                    @endif
                    <a href="{{ route('site.contact') }}" class="inline-flex items-center justify-center rounded-md bg-white text-sm font-semibold text-navy-950 transition-colors hover:bg-navy-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/70 focus-visible:ring-offset-2 focus-visible:ring-offset-navy-900 h-9 px-4">Pedir informação</a>
                </div>
            </div>

            <button
                type="button"
                class="inline-flex h-11 w-11 items-center justify-center rounded-md text-white transition-colors hover:bg-white/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/70 md:hidden"
                @click="open = !open"
                :aria-expanded="open.toString()"
                aria-controls="mobile-menu"
                aria-label="Abrir menu de navegação"
            >
                <svg x-show="!open" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 7h16"></path><path d="M4 12h16"></path><path d="M4 17h16"></path></svg>
                <svg x-show="open" x-cloak class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 6l12 12"></path><path d="M18 6 6 18"></path></svg>
            </button>
        </div>

        <div id="mobile-menu" x-show="open" x-cloak x-transition.opacity class="border-t border-white/[0.08] bg-navy-900 md:hidden">
            <nav class="mx-auto flex max-w-7xl flex-col px-4 pb-5 pt-2 sm:px-6" aria-label="Navegação mobile">
                @foreach($navItems as $item)
                    <a href="{{ route($item['route']) }}" @click="open = false"
                       @if($item['active']) aria-current="page" @endif
                       class="flex min-h-12 items-center border-b border-white/[0.08] text-base font-medium transition-colors {{ $item['active'] ? 'text-white' : 'text-white/70 hover:text-white' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
                @if($phone !== '' && $phoneHref !== '')
                    <a href="tel:{{ $phoneHref }}" class="flex min-h-12 items-center border-b border-white/[0.08] text-sm tabular-nums text-white/55 transition-colors hover:text-white/90">Ligar {{ $phone }}</a>
                @endif
                <a href="{{ route('site.contact') }}" class="inline-flex items-center justify-center rounded-md bg-white text-sm font-semibold text-navy-950 transition-colors hover:bg-navy-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/70 focus-visible:ring-offset-2 focus-visible:ring-offset-navy-900 mt-5 h-12 w-full px-5">Pedir informação</a>
            </nav>
        </div>
    </header>

    <main id="main-content" class="flex-1 overflow-x-hidden">
        @yield('content')
    </main>

    <footer class="{{ request()->routeIs('site.home') ? '' : 'mt-24' }} bg-navy-950 text-white">
        <div class="mx-auto flex max-w-7xl flex-col gap-8 px-4 py-10 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:gap-16 lg:px-8 lg:py-12">
            <div class="flex items-center gap-4 sm:gap-5">
                <a href="{{ route('site.home') }}" class="shrink-0 rounded-full focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60 focus-visible:ring-offset-2 focus-visible:ring-offset-navy-950" aria-label="{{ $businessName }}">
                    <span class="flex h-16 w-16 items-center justify-center rounded-full bg-white">
                        <img src="{{ asset('images/branding/maquivelosoLogo.png') }}" alt="" class="h-[3.75rem] w-[3.75rem] rounded-full object-contain">
                    </span>
                </a>
                <div>
                    <p class="text-xl font-semibold tracking-tight">{{ $businessName }}</p>
                    <p class="mt-1 max-w-md text-sm leading-relaxed text-white/60">
                        Venda de máquinas de costura e acessórios, com aconselhamento antes da compra e assistência técnica depois.
                    </p>
                </div>
            </div>

            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:gap-8 lg:shrink-0">
                <nav aria-label="Rodapé">
                    <ul class="flex flex-wrap gap-x-6 gap-y-2 text-sm">
                        @foreach($navItems as $item)
                            <li>
                                <a href="{{ route($item['route']) }}"
                                   @if($item['active']) aria-current="page" @endif
                                   class="inline-flex min-h-9 items-center rounded transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60 focus-visible:ring-offset-2 focus-visible:ring-offset-navy-950 {{ $item['active'] ? 'text-white' : 'text-white/65 hover:text-white' }}">{{ $item['label'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </nav>

                <span class="hidden h-5 w-px bg-white/15 sm:block" aria-hidden="true"></span>

                <a href="{{ route('site.contact') }}" class="group inline-flex items-center gap-2 self-start border-b border-white/30 pb-1 text-sm font-semibold transition-colors hover:border-white sm:self-auto focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60 focus-visible:ring-offset-2 focus-visible:ring-offset-navy-950">
                    Fale connosco
                    <svg class="h-4 w-4 transition-transform motion-safe:group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"></path><path d="M13 6l6 6-6 6"></path></svg>
                </a>
            </div>
        </div>

        <div class="border-t border-white/[0.07]">
            <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-5 text-sm text-white/50 sm:flex-row sm:items-center sm:justify-between sm:gap-8 sm:px-6 lg:px-8">
                <p>© {{ date('Y') }} {{ $businessName }}</p>

                @if(($phone !== '' && $phoneHref !== '') || $email !== '' || $location !== '')
                    <ul class="flex flex-wrap gap-x-6 gap-y-1">
                        @if($phone !== '' && $phoneHref !== '')
                            <li><a href="tel:{{ $phoneHref }}" class="rounded transition-colors hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60 focus-visible:ring-offset-2 focus-visible:ring-offset-navy-950">{{ $phone }}</a></li>
                        @endif
                        @if($email !== '')
                            <li><a href="mailto:{{ $email }}" class="break-all rounded transition-colors hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60 focus-visible:ring-offset-2 focus-visible:ring-offset-navy-950">{{ $email }}</a></li>
                        @endif
                        @if($location !== '')
                            <li>{{ preg_replace('/\s*\R\s*/', ', ', $location) }}</li>
                        @endif
                    </ul>
                @endif
            </div>
        </div>
    </footer>
</body>
</html>
