@extends('layouts.admin')

@section('title', 'Detalhes da Máquina')

@section('content')
@php
    $statusVariant = in_array($machine->status, ['available', 'reserved', 'sold', 'inactive'], true)
        ? $machine->status
        : 'neutral';
@endphp
<div class="max-w-4xl">
    <div class="mb-6 flex items-start justify-between gap-4">
        <div>
            <span class="inline-flex items-center gap-2.5 text-xs font-semibold uppercase tracking-[0.2em] text-brand-700">
                <span class="h-px w-7 stitch-line"></span>Máquinas
            </span>
            <h1 class="mt-2 font-serif text-3xl font-semibold tracking-tight text-stone-900">{{ $machine->name }}</h1>
        </div>

        <div class="inline-flex gap-2">
            <x-ui.button :href="route('admin.machines.edit', $machine)" variant="outline" size="sm">Editar</x-ui.button>

            <form method="POST" action="{{ route('admin.machines.destroy', $machine) }}" onsubmit="return confirm('Eliminar esta máquina?');">
                @csrf
                @method('DELETE')
                <x-ui.button type="submit" variant="danger-outline" size="sm">Apagar</x-ui.button>
            </form>
        </div>
    </div>

    <div class="space-y-5 rounded-2xl border border-stone-200 bg-white p-6 shadow-card sm:p-8">
        <div class="flex flex-wrap items-center gap-2">
            <x-ui.badge :variant="$statusVariant">{{ $machine->status_label }}</x-ui.badge>
            @if($machine->category)
                <x-ui.badge variant="neutral">{{ $machine->category->name }}</x-ui.badge>
            @endif
            @if($machine->negotiable)
                <x-ui.badge variant="negotiable">Negociável</x-ui.badge>
            @endif
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div>
                <div class="text-xs font-semibold uppercase tracking-wide text-stone-500">Marca</div>
                <div class="mt-1 text-sm text-stone-900">{{ $machine->brand ?? '—' }}</div>
            </div>
            <div>
                <div class="text-xs font-semibold uppercase tracking-wide text-stone-500">Modelo</div>
                <div class="mt-1 text-sm text-stone-900">{{ $machine->model ?? '—' }}</div>
            </div>
            <div>
                <div class="text-xs font-semibold uppercase tracking-wide text-stone-500">Preço</div>
                <div class="mt-1 font-serif text-xl font-semibold text-stone-900">{{ $machine->price_formatted ?? 'Sob consulta' }}</div>
            </div>
            <div>
                <div class="text-xs font-semibold uppercase tracking-wide text-stone-500">Estado</div>
                <div class="mt-1 text-sm text-stone-900">{{ $machine->status_label }}</div>
            </div>
        </div>

        <div class="border-t border-stone-100 pt-5">
            <div class="text-xs font-semibold uppercase tracking-wide text-stone-500">Descrição</div>
            <div class="mt-2 whitespace-pre-line text-sm leading-relaxed text-stone-700">{{ $machine->description ?? '—' }}</div>
        </div>

        <div class="pt-1">
            <a href="{{ route('admin.machines.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-stone-600 transition hover:text-brand-700">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 12H5"></path><path d="M11 5l-7 7 7 7"></path></svg>
                Voltar à lista
            </a>
        </div>
    </div>
</div>
@endsection
