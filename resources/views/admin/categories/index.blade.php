@extends('layouts.admin')

@section('title', 'Categorias')

@section('content')
<div class="flex items-start justify-between">
    <div>
        <h1 class="text-4xl font-semibold tracking-tight">Categorias</h1>
        <p class="text-gray-500 mt-2">Gere as categorias usadas nas máquinas.</p>
    </div>
</div>

@if (session('success'))
    <div class="mt-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="mt-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl">
        <div class="font-semibold mb-2">Corrige os erros abaixo:</div>
        <ul class="list-disc pl-5 space-y-1 text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="mt-8 bg-white border rounded-2xl p-6">

    <form method="POST" action="{{ route('admin.categories.store') }}" class="flex flex-col md:flex-row gap-4 mb-8">
        @csrf

        <div class="flex-1">
            <input
                name="name"
                required
                value="{{ old('name') }}"
                placeholder="Nome da nova categoria"
                class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-gray-400 focus:ring-gray-400"
            />
        </div>

        <button
            class="inline-flex items-center justify-center gap-2 bg-gray-900 text-white px-6 py-3 rounded-xl font-medium hover:bg-gray-800">
            + Adicionar
        </button>
    </form>

    <form method="GET" class="flex flex-col md:flex-row gap-4 mb-6">
        <div class="flex-1">
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
                <input
                    name="q"
                    value="{{ $q ?? '' }}"
                    placeholder="Pesquisar por nome..."
                    class="w-full pl-11 pr-4 py-3 rounded-xl border-gray-200 focus:border-gray-400 focus:ring-gray-400"
                />
            </div>
        </div>

        <button class="px-5 py-3 rounded-xl border border-gray-200 hover:bg-gray-50 font-medium">
            Filtrar
        </button>

        @if(($q ?? '') !== '')
            <a href="{{ route('admin.categories.index') }}"
               class="px-5 py-3 rounded-xl border border-gray-200 hover:bg-gray-50 font-medium text-gray-600">
                Limpar
            </a>
        @endif
    </form>

    <div class="overflow-hidden rounded-2xl border border-gray-100">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500">
                <tr>
                    <th class="text-left px-4 py-3 font-medium">Nome</th>
                    <th class="text-right px-4 py-3 font-medium w-64">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($categories as $category)
                    <tr>
                        <td class="px-4 py-4">
                            <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')

                                <input
                                    name="name"
                                    value="{{ $category->name }}"
                                    class="w-full px-3 py-2 rounded-xl border-gray-200 focus:border-gray-400 focus:ring-gray-400 text-sm font-medium"
                                />

                                <button class="shrink-0 px-3 py-2 rounded-xl border border-gray-200 hover:bg-gray-50 font-medium">
                                    Guardar
                                </button>
                            </form>
                        </td>

                        <td class="px-4 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <form method="POST"
                                      action="{{ route('admin.categories.destroy', $category) }}"
                                      onsubmit="return confirm('Remover esta categoria?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-2 rounded-xl border border-gray-200 hover:bg-gray-50 font-medium text-red-600">
                                        Remover
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-4 py-10 text-center text-gray-500">
                            Sem categorias.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $categories->links() }}
    </div>
</div>
@endsection
