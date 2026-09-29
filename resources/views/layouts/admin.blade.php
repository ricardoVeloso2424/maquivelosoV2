{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'MaquiVeloso'))</title>
    <link rel="icon" type="image/png" href="{{ asset('images/branding/maquivelosoLogo.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-stone-100 font-sans text-stone-800">
    <div class="relative isolate flex min-h-screen">
        <aside class="relative z-50 flex w-72 flex-col border-r border-stone-200 bg-white">
            <div class="flex items-center gap-3 px-6 pb-6 pt-8">
                <img src="{{ asset('images/branding/maquivelosoLogo.png') }}" alt="MaquiVeloso" class="h-10 w-10 object-contain">

                <div class="leading-tight">
                    <div class="font-serif text-lg font-semibold tracking-tight text-stone-900">Maquiveloso</div>
                    <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-stone-400">Área de Gestão</div>
                </div>
            </div>

            <nav class="px-4">
                @php
                    $item = "flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition";
                    $active = "bg-stone-900 text-white shadow-sm";
                    $inactive = "text-stone-600 hover:bg-stone-100 hover:text-stone-900";
                @endphp

                <a href="{{ route('admin.dashboard') }}"
                   class="{{ $item }} {{ request()->routeIs('admin.dashboard') ? $active : $inactive }}">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7" rx="2"></rect>
                        <rect x="14" y="3" width="7" height="7" rx="2"></rect>
                        <rect x="3" y="14" width="7" height="7" rx="2"></rect>
                        <rect x="14" y="14" width="7" height="7" rx="2"></rect>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('admin.machines.index') }}"
                   class="mt-2 {{ $item }} {{ request()->routeIs('admin.machines.*') ? $active : $inactive }}">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 8a2 2 0 0 1-1 1.73l-7 4a2 2 0 0 1-2 0l-7-4A2 2 0 0 1 3 8"></path>
                        <path d="M3 8V16a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16V8"></path>
                        <path d="M3 8l9-5 9 5"></path>
                    </svg>
                    Máquinas
                </a>

                <a href="{{ route('admin.categories.index') }}"
                   class="mt-2 {{ $item }} {{ request()->routeIs('admin.categories.*') ? $active : $inactive }}">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.59 13.41 11 3H4v7l9.59 9.59a2 2 0 0 0 2.82 0l4.18-4.18a2 2 0 0 0 0-2.82Z"></path>
                        <path d="M7 7h.01"></path>
                    </svg>
                    Categorias
                </a>

                <a href="{{ route('admin.settings') }}"
                   class="mt-2 {{ $item }} {{ request()->routeIs('admin.settings') ? $active : $inactive }}">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                    Definições
                </a>

                <div class="my-6 border-t border-stone-200"></div>

                <a href="{{ route('site.home') }}" target="_blank"
                   class="{{ $item }} {{ $inactive }}">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 3h7v7"></path>
                        <path d="M10 14L21 3"></path>
                        <path d="M21 14v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h6"></path>
                    </svg>
                    Ver Site
                </a>

                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="w-full text-left {{ $item }} text-red-600 hover:bg-red-50">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <path d="M16 17l5-5-5-5"></path>
                            <path d="M21 12H9"></path>
                        </svg>
                        Terminar Sessão
                    </button>
                </form>
            </nav>

            <div class="mt-auto px-6 py-6 text-xs text-stone-400">
                {{ config('app.name', 'MaquiVeloso') }}
            </div>
        </aside>

        <main class="relative z-10 flex-1">
            <div class="mx-auto max-w-6xl px-6 py-10 sm:px-10">
                @include('admin.partials.flash')
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
