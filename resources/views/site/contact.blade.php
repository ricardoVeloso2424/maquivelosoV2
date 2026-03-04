@extends('layouts.site')

@section('content')
<section class="max-w-3xl mx-auto px-6 py-16">
    <h1 class="text-3xl font-bold mb-8">Contacto</h1>

    <div class="bg-white border rounded-xl p-6 space-y-4">
        <p class="text-sm text-gray-700">
            O formulário de contacto online está temporariamente indisponível.
        </p>
        <p class="text-sm text-gray-700">
            Para pedidos, por favor utilize os contactos no rodapé do site.
        </p>
        <a href="{{ route('site.home') }}" class="inline-flex px-6 py-3 bg-gray-900 text-white rounded-lg font-medium hover:bg-gray-800">
            Voltar ao início
        </a>
    </div>
</section>
@endsection
