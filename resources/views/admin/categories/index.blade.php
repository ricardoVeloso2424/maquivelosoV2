@extends('layouts.admin')

@section('title', 'Categorias')

@section('content')
<div class="flex items-start justify-between">
    <div>
        <span class="inline-flex items-center gap-2.5 text-xs font-semibold uppercase tracking-[0.2em] text-brand-700">
            <span class="h-px w-7 stitch-line"></span>Organização
        </span>
        <h1 class="mt-2 font-serif text-4xl font-semibold tracking-tight text-stone-900">Categorias</h1>
        <p class="mt-2 text-sm text-stone-500">Gere as categorias usadas nas máquinas.</p>
    </div>
</div>

@if ($errors->any())
    <div class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
        <div class="mb-2 font-semibold">Corrige os erros abaixo:</div>
        <ul class="list-disc space-y-1 pl-5 text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="mt-8 rounded-2xl border border-stone-200 bg-white p-6 shadow-card">
    <form method="POST" action="{{ route('admin.categories.store') }}" class="mb-8 flex flex-col gap-3 md:flex-row">
        @csrf
        <div class="flex-1">
            <input
                name="name"
                required
                value="{{ old('name') }}"
                placeholder="Nome da nova categoria"
                class="w-full rounded-xl border-stone-300 px-4 py-3 text-sm transition focus:border-brand-500 focus:ring-brand-500"
            />
        </div>
        <x-ui.button type="submit" variant="primary" size="md">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg>
            Adicionar
        </x-ui.button>
    </form>

    <form method="GET" class="mb-6 flex flex-col gap-3 md:flex-row">
        <div class="flex-1">
            <div class="relative">
                <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-stone-400">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"></circle><path d="M21 21l-4.3-4.3"></path></svg>
                </span>
                <input
                    name="q"
                    value="{{ $q ?? '' }}"
                    placeholder="Pesquisar por nome..."
                    class="w-full rounded-xl border-stone-300 py-3 pl-12 pr-4 text-sm transition focus:border-brand-500 focus:ring-brand-500"
                />
            </div>
        </div>

        <x-ui.button type="submit" variant="outline" size="md">Filtrar</x-ui.button>

        @if(($q ?? '') !== '')
            <x-ui.button :href="route('admin.categories.index')" variant="ghost" size="md">Limpar</x-ui.button>
        @endif
    </form>

    <div class="overflow-hidden rounded-2xl border border-stone-200">
        <table class="w-full text-sm">
            <thead class="bg-stone-50 text-stone-500">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold">Nome</th>
                    <th class="w-64 px-4 py-3 text-right font-semibold">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($categories as $category)
                    <tr class="transition hover:bg-stone-50">
                        <td class="px-4 py-4">
                            <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <input
                                    name="name"
                                    value="{{ $category->name }}"
                                    class="w-full rounded-xl border-stone-300 px-3 py-2 text-sm font-medium transition focus:border-brand-500 focus:ring-brand-500"
                                />
                                <x-ui.button type="submit" variant="outline" size="sm" class="shrink-0">Guardar</x-ui.button>
                            </form>
                        </td>

                        <td class="px-4 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <form method="POST"
                                      action="{{ route('admin.categories.destroy', $category) }}"
                                      onsubmit="return confirm('Remover esta categoria?');">
                                    @csrf
                                    @method('DELETE')
                                    <x-ui.button type="submit" variant="danger-outline" size="sm">Remover</x-ui.button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-4 py-10 text-center text-stone-500">Sem categorias.</td>
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
