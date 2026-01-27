<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>MaquiVeloso</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-900 flex flex-col min-h-screen">

    {{-- Header --}}
    <header class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="{{ route('site.home') }}" class="text-xl font-bold">
                MaquiVeloso
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

    {{-- Content --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-900 text-gray-300 mt-16">
        <div class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <div class="text-lg font-semibold text-white mb-3">MaquiVeloso</div>
                <p class="text-sm">
                    Reparação, manutenção e venda de máquinas de costura.
                </p>
            </div>

            <div>
                <div class="text-sm font-semibold text-white mb-3">Links</div>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('site.home') }}" class="hover:text-white">Início</a></li>
                    <li><a href="{{ route('site.catalog') }}" class="hover:text-white">Catálogo</a></li>
                    <li><a href="{{ route('site.contact') }}" class="hover:text-white">Contacto</a></li>
                </ul>
            </div>

            <div>
                <div class="text-sm font-semibold text-white mb-3">Contacto</div>
                <ul class="space-y-2 text-sm">
                    <li>Telefone: 21 123 4567</li>
                    <li>Rua das Flores, 123<br>1000-001 Lisboa</li>
                    <li>Segunda a Sexta: 9h – 18h<br>Sábado: 9h – 13h</li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-800 text-center text-sm py-4">
            © {{ date('Y') }} MaquiVeloso. Todos os direitos reservados.
        </div>
    </footer>

</body>

</html>