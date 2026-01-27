@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <div class="bg-white rounded-xl border p-6">
        <div class="text-sm text-gray-500">Total de Máquinas</div>
        <div class="mt-2 text-3xl font-bold">0</div>
    </div>

    <div class="bg-white rounded-xl border p-6">
        <div class="text-sm text-gray-500">Disponíveis</div>
        <div class="mt-2 text-3xl font-bold">0</div>
    </div>

    <div class="bg-white rounded-xl border p-6">
        <div class="text-sm text-gray-500">Vendidas</div>
        <div class="mt-2 text-3xl font-bold">0</div>
    </div>

</div>

<div class="mt-10 bg-white rounded-xl border p-6">
    <h2 class="text-lg font-semibold mb-4">Resumo</h2>

    <p class="text-sm text-gray-600">
        Este painel serve para gerir as máquinas de costura, estados, imagens e
        informações de contacto do negócio.
    </p>
</div>
@endsection