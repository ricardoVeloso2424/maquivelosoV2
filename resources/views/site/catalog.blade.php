@extends('layouts.site')

@section('content')
<section class="max-w-7xl mx-auto px-6 py-16">
    <h1 class="text-3xl font-bold mb-8">Catálogo</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">

        {{-- Exemplo estático --}}
        <div class="bg-white border rounded-xl overflow-hidden">
            <div class="h-48 bg-gray-100 flex items-center justify-center text-gray-400">
                Imagem
            </div>

            <div class="p-4">
                <h3 class="font-semibold">Máquina Exemplo</h3>
                <p class="text-sm text-gray-600 mb-2">Singer</p>
                <p class="font-bold mb-3">250 €</p>

                <a href="{{ route('site.contact') }}"
                    class="inline-block text-sm font-medium text-gray-900 hover:underline">
                    Pedir informações
                </a>
            </div>
        </div>

        <div class="border rounded-xl p-8 text-center text-gray-500 col-span-full">
            Mais máquinas serão adicionadas brevemente.
        </div>

    </div>
</section>
@endsection