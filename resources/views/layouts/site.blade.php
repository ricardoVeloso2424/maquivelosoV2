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

    <header x-data="{ open: false }" @keydown.escape.window="open = false" class="sticky top-0 z-50 bg-navy-900 text-white">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-6 px-4 sm:px-6 lg:px-8">
            <a href="{{ route('site.home') }}" class="inline-flex items-center gap-3 rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-navy-900">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-white">
                    <img src="{{ asset('images/branding/maquivelosoLogo.png') }}" alt="" class="h-10 w-10 rounded-full object-contain">
                </span>
                <span class="text-lg font-semibold tracking-tight">{{ $businessName }}</span>
            </a>

            <nav class="hidden h-full items-center gap-1 md:flex" aria-label="Navegação principal">
                @foreach($navItems as $item)
                    <a href="{{ route($item['route']) }}"
                       @if($item['active']) aria-current="page" @endif
                       class="relative flex h-full items-center px-4 text-sm font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-white {{ $item['active'] ? 'text-white after:absolute after:inset-x-4 after:bottom-0 after:h-0.5 after:bg-white' : 'text-navy-100 hover:text-white' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="hidden items-center gap-5 md:flex">
                @if($phone !== '' && $phoneHref !== '')
                    <a href="tel:{{ $phoneHref }}" class="hidden text-sm font-medium text-navy-100 transition hover:text-white lg:inline">{{ $phone }}</a>
                @endif
                <x-ui.button :href="route('site.contact')" variant="navy-on-dark" shape="rounded" size="sm">
                    Pedir informação
                </x-ui.button>
            </div>

            <button
                type="button"
                class="inline-flex h-11 w-11 items-center justify-center rounded-lg text-white transition hover:bg-white/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white md:hidden"
                @click="open = !open"
                :aria-expanded="open.toString()"
                aria-controls="mobile-menu"
                aria-label="Abrir menu de navegação"
            >
                <svg x-show="!open" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 7h16"></path><path d="M4 12h16"></path><path d="M4 17h16"></path></svg>
                <svg x-show="open" x-cloak class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 6l12 12"></path><path d="M18 6 6 18"></path></svg>
            </button>
        </div>

        <div id="mobile-menu" x-show="open" x-cloak x-transition.opacity class="border-t border-white/10 bg-navy-900 md:hidden">
            <nav class="mx-auto flex max-w-7xl flex-col px-4 pb-5 pt-2" aria-label="Navegação mobile">
                @foreach($navItems as $item)
                    <a href="{{ route($item['route']) }}" @click="open = false"
                       @if($item['active']) aria-current="page" @endif
                       class="flex min-h-12 items-center border-b border-white/10 text-base font-medium {{ $item['active'] ? 'text-white' : 'text-navy-100' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
                @if($phone !== '' && $phoneHref !== '')
                    <a href="tel:{{ $phoneHref }}" class="flex min-h-12 items-center border-b border-white/10 text-base font-medium text-navy-100">Ligar {{ $phone }}</a>
                @endif
                <x-ui.button :href="route('site.contact')" variant="navy-on-dark" shape="rounded" size="lg" class="mt-5 w-full">Pedir informação</x-ui.button>
            </nav>
        </div>
    </header>

    <main id="main-content" class="flex-1 overflow-x-hidden">
        @yield('content')
    </main>

    <footer class="{{ request()->routeIs('site.home') ? '' : 'mt-24' }} bg-navy-950 text-navy-100">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 md:grid-cols-12 lg:px-8">
            <div class="md:col-span-5">
                <div class="flex items-center gap-3">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-white">
                        <img src="{{ asset('images/branding/maquivelosoLogo.png') }}" alt="" class="h-10 w-10 rounded-full object-contain">
                    </span>
                    <span class="text-lg font-semibold text-white">{{ $businessName }}</span>
                </div>
                <p class="mt-4 max-w-sm text-sm leading-relaxed">
                    Venda de máquinas de costura e acessórios, com aconselhamento antes da compra e assistência técnica depois.
                </p>
            </div>

            <nav class="md:col-span-3" aria-label="Rodapé">
                <h2 class="text-sm font-semibold text-white">Site</h2>
                <ul class="mt-4 space-y-1 text-sm">
                    @foreach($navItems as $item)
                        <li><a href="{{ route($item['route']) }}" class="inline-flex min-h-8 items-center transition hover:text-white">{{ $item['label'] }}</a></li>
                    @endforeach
                </ul>
            </nav>

            <div class="md:col-span-4">
                <h2 class="text-sm font-semibold text-white">Contacto</h2>
                <ul class="mt-4 space-y-1 text-sm">
                    @if($phone !== '' && $phoneHref !== '')
                        <li><a href="tel:{{ $phoneHref }}" class="inline-flex min-h-8 items-center transition hover:text-white">{{ $phone }}</a></li>
                    @endif
                    @if($email !== '')
                        <li><a href="mailto:{{ $email }}" class="inline-flex min-h-8 items-center break-all transition hover:text-white">{{ $email }}</a></li>
                    @endif
                    @if($location !== '')
                        <li class="whitespace-pre-line py-1.5">{{ $location }}</li>
                    @endif
                    @if($hours !== '')
                        <li class="whitespace-pre-line py-1.5">{{ $hours }}</li>
                    @endif
                    @if($phone === '' && $email === '' && $location === '' && $hours === '')
                        <li><a href="{{ route('site.contact') }}" class="inline-flex min-h-8 items-center transition hover:text-white">Página de contacto</a></li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="border-t border-white/10">
            <p class="mx-auto max-w-7xl px-4 py-5 text-xs text-navy-300 sm:px-6 lg:px-8">© {{ date('Y') }} {{ $businessName }}</p>
        </div>
    </footer>
</body>
</html>
