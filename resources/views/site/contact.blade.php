@extends('layouts.site')

@section('content')
<section class="max-w-3xl mx-auto px-6 py-16">
    <h1 class="text-3xl font-bold mb-8">Contacto</h1>

    <div class="bg-white border rounded-xl p-6">
        <form class="space-y-6">

            <div>
                <label class="block text-sm font-medium mb-1">Nome</label>
                <input type="text"
                    class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email"
                    class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Mensagem</label>
                <textarea rows="4"
                    class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"></textarea>
            </div>

            <button
                type="submit"
                class="px-6 py-3 bg-gray-900 text-white rounded-lg font-medium hover:bg-gray-800">
                Enviar mensagem
            </button>

        </form>
    </div>
</section>
@endsection