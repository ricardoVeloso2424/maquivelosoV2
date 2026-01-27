<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        @hasSection('title')
            @yield('title') - {{ config('app.name', 'MaquiVeloso') }}
        @else
            {{ config('app.name', 'MaquiVeloso') }}
        @endif
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900">
<div class="min-h-screen">

    <header class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <nav class="flex items-center gap-6 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="font-semibold">Dashboard</a>
                <a href="{{ route('admin.machines.index') }}">Máquinas</a>
                <a href="{{ route('admin.settings') }}">Definições</a>
            </nav>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-sm px-3 py-2 rounded border bg-white hover:bg-gray-50">
                    Logout
                </button>
            </form>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-8">
        @hasSection('header')
            <h1 class="text-xl font-semibold mb-6">@yield('header')</h1>
        @endif

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-300 rounded">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>

</div>
</body>
</html>
