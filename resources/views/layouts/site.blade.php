<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $siteSettings['business_name'] ?? 'MaquiVeloso' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased flex flex-col" style="font-family: 'Space Grotesk', ui-sans-serif, system-ui, sans-serif;">
    @php
        $businessName = $siteSettings['business_name'] ?? 'MaquiVeloso';
        $phone = $siteSettings['contact_phone'] ?? ($siteSettings['phone'] ?? '');
        $email = $siteSettings['contact_email'] ?? ($siteSettings['email'] ?? '');
        $location = $siteSettings['contact_address'] ?? ($siteSettings['location'] ?? '');
    @endphp

    <header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/90 backdrop-blur">
        <div class="mx-auto flex h-20 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            <a href="{{ route('site.home') }}" class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">
                {{ $businessName }}
            </a>

            <nav class="flex items-center gap-1 sm:gap-2 text-sm font-semibold">
                <a href="{{ route('site.home') }}"
                   class="rounded-full px-3 py-2 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2 {{ request()->routeIs('site.home') ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    Início
                </a>
                <a href="{{ route('site.catalog') }}"
                   class="rounded-full px-3 py-2 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2 {{ request()->routeIs('site.catalog') || request()->routeIs('site.machine.show') ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    Catálogo
                </a>
                <a href="{{ route('site.contact') }}"
                   class="rounded-full px-3 py-2 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2 {{ request()->routeIs('site.contact') ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    Contacto
                </a>
            </nav>

            <a href="{{ route('site.contact') }}"
               class="hidden rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-800 transition hover:border-slate-900 hover:text-slate-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2 md:inline-flex">
                Pedir informação
            </a>
        </div>
    </header>

    <main class="flex-1 overflow-x-hidden">
        @yield('content')
    </main>

    <footer class="mt-20 border-t border-slate-800 bg-slate-950 text-slate-300">
        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-4 py-12 sm:px-6 lg:grid-cols-3 lg:px-8">
            <div>
                <div class="text-lg font-semibold text-white">{{ $businessName }}</div>
                <p class="mt-3 max-w-sm text-sm leading-relaxed text-slate-400">
                    Venda, revisão e acompanhamento para máquinas de costura domésticas e industriais.
                </p>
            </div>

            <div>
                <div class="text-sm font-semibold uppercase tracking-wide text-white">Navegação</div>
                <ul class="mt-3 space-y-2 text-sm text-slate-400">
                    <li><a href="{{ route('site.home') }}" class="rounded transition hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950">Início</a></li>
                    <li><a href="{{ route('site.catalog') }}" class="rounded transition hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950">Catálogo</a></li>
                    <li><a href="{{ route('site.contact') }}" class="rounded transition hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950">Contacto</a></li>
                </ul>
            </div>

            <div>
                <div class="text-sm font-semibold uppercase tracking-wide text-white">Contacto</div>
                <ul class="mt-3 space-y-2 text-sm text-slate-400">
                    @if($phone !== '')
                        <li>Telefone: {{ $phone }}</li>
                    @endif

                    @if($email !== '')
                        <li>Email: {{ $email }}</li>
                    @endif

                    @if($location !== '')
                        <li class="whitespace-pre-line">{!! nl2br(e($location)) !!}</li>
                    @endif

                    @if($phone === '' && $email === '' && $location === '')
                        <li>Contactos não configurados no backoffice.</li>
                    @endif
                </ul>
            </div>
        </div>
    </footer>
</body>
</html>
