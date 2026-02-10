<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $siteSettings['business_name'] ?? 'MaquiVeloso' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-900 flex flex-col min-h-screen">
    @php
        $businessName = $siteSettings['business_name'] ?? 'MaquiVeloso';
        $phone = $siteSettings['phone'] ?? '';
        $email = $siteSettings['email'] ?? '';
        $location = $siteSettings['location'] ?? '';
    @endphp

    <header class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="{{ route('site.home') }}" class="text-xl font-bold">
                {{ $businessName }}
            </a>

            <nav class="flex items-center gap-6 text-sm font-medium">
                <a href="{{ route('site.home') }}"
                   class="{{ request()->routeIs('site.home') ? 'text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                    Início
                </a>
                <a href="{{ route('site.catalog') }}"
                   class="{{ request()->routeIs('site.catalog') ? 'text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                    Catálogo
                </a>
                <a href="{{ route('site.contact') }}"
                   class="{{ request()->routeIs('site.contact') ? 'text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                    Contacto
                </a>
            </nav>
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="bg-gray-900 text-gray-300 mt-16">
        <div class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <div class="text-lg font-semibold text-white mb-3">{{ $businessName }}</div>
                <p class="text-sm">
                    Reparação, manutenção e venda de máquinas de costura.
                </p>
            </div>

            <div>
                <div class="text-sm font-semibold text-white mb-3">Contacto</div>
                <ul class="space-y-2 text-sm">
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
