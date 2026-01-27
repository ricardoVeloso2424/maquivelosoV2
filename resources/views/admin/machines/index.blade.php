@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Máquinas</h1>
            <p class="text-sm text-gray-600">Gerir catálogo de máquinas.</p>
        </div>

        <a href="{{ route('admin.machines.create') }}"
            class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
            + Nova máquina
        </a>
    </div>

    @if (session('success'))
    <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
        {{ session('success') }}
    </div>
    @endif

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Nome</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Marca</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Modelo</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Preço</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Estado</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Ações</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                @forelse ($machines as $machine)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm font-medium text-gray-900">
                        <a class="hover:underline" href="{{ route('admin.machines.show', $machine) }}">
                            {{ $machine->name }}
                        </a>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700">{{ $machine->brand ?? '—' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">{{ $machine->model ?? '—' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">
                        {{ $machine->price !== null ? number_format((float)$machine->price, 2, ',', '.') . ' €' : '—' }}
                    </td>
                    <td class="px-4 py-3 text-sm">
                        @if ($machine->status === 'active')
                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Ativa</span>
                        @else
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">Inativa</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-right">
                        <div class="inline-flex items-center gap-2">
                            <a href="{{ route('admin.machines.edit', $machine) }}"
                                class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-100">
                                Editar
                            </a>

                            <form method="POST" action="{{ route('admin.machines.destroy', $machine) }}"
                                onsubmit="return confirm('Eliminar esta máquina?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="rounded-md border border-red-300 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-50">
                                    Apagar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-600">
                        Ainda não há máquinas. Clica em “Nova máquina”.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $machines->links() }}
    </div>
</div>
@endsection