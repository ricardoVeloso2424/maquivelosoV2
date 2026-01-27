@extends('layouts.site')

@section('content')
<section class="bg-white">
    <div class="max-w-7xl mx-auto px-6 py-20 text-center">
        <h1 class="text-4xl font-bold mb-6">
            Máquinas de costura com confiança
        </h1>

        <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-10">
            Reparação, manutenção e venda de máquinas de costura.
            Experiência, qualidade e acompanhamento personalizado.
        </p>

        <a href="{{ route('site.catalog') }}"
            class="inline-block px-6 py-3 bg-gray-900 text-white rounded-lg font-medium hover:bg-gray-800">
            Ver catálogo
        </a>
    </div>
</section>

<section class="max-w-7xl mx-auto px-6 py-16 grid grid-cols-1 md:grid-cols-3 gap-8">
    <div class="bg-white border rounded-xl p-6 text-center">
        <h3 class="font-semibold text-lg mb-2">Reparações</h3>
        <p class="text-sm text-gray-600">
            Diagnóstico e reparação de máquinas domésticas e industriais.
        </p>
    </div>

    <div class="bg-white border rounded-xl p-6 text-center">
        <h3 class="font-semibold text-lg mb-2">Venda</h3>
        <p class="text-sm text-gray-600">
            Máquinas revistas, prontas a trabalhar e com garantia.
        </p>
    </div>

    <div class="bg-white border rounded-xl p-6 text-center">
        <h3 class="font-semibold text-lg mb-2">Manutenção</h3>
        <p class="text-sm text-gray-600">
            Limpeza, afinação e prolongamento da vida útil da máquina.
        </p>
    </div>
</section>
@endsection