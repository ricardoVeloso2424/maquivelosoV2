@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Nova máquina</h1>
        <p class="text-sm text-gray-600">Adicionar uma nova máquina ao catálogo.</p>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6">
        <form method="POST" action="{{ route('admin.machines.store') }}">
            @csrf

            @include('admin.machines._form')

            <div class="mt-6 flex items-center justify-between">
                <a href="{{ route('admin.machines.index') }}"
                    class="text-sm font-medium text-gray-700 hover:underline">
                    Voltar
                </a>

                <button type="submit"
                    class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection