{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'MaquiVeloso'))</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans bg-gray-50 text-gray-900">
    <div class="min-h-screen flex relative isolate">
        <aside class="w-72 bg-white border-r border-gray-100 flex flex-col relative z-50">
            <div class="px-6 pt-8 pb-6">
                <div class="text-xl font-extrabold tracking-tight">Maquiveloso</div>
                <div class="text-sm text-gray-500 mt-1">Área de Gestão</div>
            </div>

            <nav class="px-4">
                @php
                    $item = "flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition";
                    $active = "bg-gray-900 text-white shadow-sm";
                    $inactive = "text-gray-700 hover:bg-gray-100";
                @endphp

                <a href="{{ route('admin.dashboard') }}"
                   class="{{ $item }} {{ request()->routeIs('admin.dashboard') ? $active : $inactive }}">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7" rx="2"></rect>
                        <rect x="14" y="3" width="7" height="7" rx="2"></rect>
                        <rect x="3" y="14" width="7" height="7" rx="2"></rect>
                        <rect x="14" y="14" width="7" height="7" rx="2"></rect>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('admin.machines.index') }}"
                   class="mt-2 {{ $item }} {{ request()->routeIs('admin.machines.*') ? $active : $inactive }}">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 8a2 2 0 0 1-1 1.73l-7 4a2 2 0 0 1-2 0l-7-4A2 2 0 0 1 3 8"></path>
                        <path d="M3 8V16a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16V8"></path>
                        <path d="M3 8l9-5 9 5"></path>
                    </svg>
                    Máquinas
                </a>

                <a href="{{ route('admin.categories.index') }}"
                   class="mt-2 {{ $item }} {{ request()->routeIs('admin.categories.*') ? $active : $inactive }}">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.59 13.41 11 3H4v7l9.59 9.59a2 2 0 0 0 2.82 0l4.18-4.18a2 2 0 0 0 0-2.82Z"></path>
                        <path d="M7 7h.01"></path>
                    </svg>
                    Categorias
                </a>

                <a href="{{ route('admin.settings') }}"
                   class="mt-2 {{ $item }} {{ request()->routeIs('admin.settings') ? $active : $inactive }}">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"></path>
                        <path d="M19.4 15a7.8 7.8 0 0 0 .1-1 7.8 7.8 0 0 0-.1-1l2-1.6-2-3.4-2.4 1a7.5 7.5 0 0 0-1.7-1l-.3-2.6H9.4L9.1 7a7.5 7.5 0 0 0-1.7 1l-2.4-1-2 3.4 2 1.6a7.8 7.8 0 0 0-.1 1 7.8 7.8 0 0 0 .1 1l-2 1.6 2 3.4 2.4-1a7.5 7.5 0 0 0 1.7 1l.3 2.6h5.2l.3-2.6a7.5 7.5 0 0 0 1.7-1l2.4 1 2-3.4-2-1.6z"></path>
                    </svg>
                    Definições
                </a>

                <div class="my-6 border-t border-gray-100"></div>

                <a href="{{ route('site.home') }}" target="_blank"
                   class="{{ $item }} {{ $inactive }}">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 3h7v7"></path>
                        <path d="M10 14L21 3"></path>
                        <path d="M21 14v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h6"></path>
                    </svg>
                    Ver Site
                </a>

                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="w-full text-left {{ $item }} text-red-600 hover:bg-red-50">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <path d="M16 17l5-5-5-5"></path>
                            <path d="M21 12H9"></path>
                        </svg>
                        Terminar Sessão
                    </button>
                </form>
            </nav>

            <div class="mt-auto px-6 py-6 text-xs text-gray-400">
                {{ config('app.name', 'MaquiVeloso') }}
            </div>
        </aside>

        <main class="flex-1 relative z-10">
            <div class="max-w-6xl mx-auto px-10 py-10">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
