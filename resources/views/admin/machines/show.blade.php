@extends('layouts.admin')

@section('title', 'Detalhes da Máquina')

@section('content')
<div class="max-w-4xl">
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">{{ $machine->name }}</h1>
            <p class="text-sm text-gray-600">Detalhes da máquina.</p>
        </div>

        <div class="inline-flex gap-2">
            <a href="{{ route('admin.machines.edit', $machine) }}"
                class="rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">
                Editar
            </a>

            <form method="POST" action="{{ route('admin.machines.destroy', $machine) }}"
                onsubmit="return confirm('Eliminar esta máquina?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="rounded-md border border-red-300 px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-50">
                    Apagar
                </button>
            </form>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <div class="text-xs font-semibold text-gray-500">Categoria</div>
                <div class="text-sm text-gray-900">{{ $machine->category->name ?? '—' }}</div>
            </div>
            <div>
                <div class="text-xs font-semibold text-gray-500">Marca</div>
                <div class="text-sm text-gray-900">{{ $machine->brand ?? '—' }}</div>
            </div>
            <div>
                <div class="text-xs font-semibold text-gray-500">Modelo</div>
                <div class="text-sm text-gray-900">{{ $machine->model ?? '—' }}</div>
            </div>
            <div>
                <div class="text-xs font-semibold text-gray-500">Preço</div>
                <div class="text-sm text-gray-900">{{ $machine->price_formatted ?? '—' }}</div>
            </div>
            <div>
                <div class="text-xs font-semibold text-gray-500">Estado</div>
                <div class="text-sm text-gray-900">{{ $machine->status_label }}</div>
            </div>
            <div>
                <div class="text-xs font-semibold text-gray-500">Negociável</div>
                <div class="text-sm text-gray-900">{{ $machine->negotiable ? 'Sim' : 'Não' }}</div>
            </div>
        </div>

        <div>
            <div class="text-xs font-semibold text-gray-500 mb-1">Descrição</div>
            <div class="text-sm text-gray-900 whitespace-pre-line">{{ $machine->description ?? '—' }}</div>
        </div>

        <div class="pt-2">
            <a href="{{ route('admin.machines.index') }}" class="text-sm font-medium text-gray-700 hover:underline">
                Voltar à lista
            </a>
        </div>
    </div>
</div>
@endsection
