@extends('layouts.admin')

@section('title', 'Nova Máquina')

@section('content')
<div class="max-w-4xl">
    <div class="flex items-start justify-between">
        <div>
            <span class="inline-flex items-center gap-2.5 text-xs font-semibold uppercase tracking-[0.2em] text-brand-700">
                <span class="h-px w-7 stitch-line"></span>Máquinas
            </span>
            <h1 class="mt-2 font-serif text-3xl font-semibold tracking-tight text-stone-900">Nova Máquina</h1>
        </div>

        <a href="{{ route('admin.machines.index') }}"
           class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-stone-200 bg-white text-stone-700 transition hover:border-brand-500 hover:text-brand-700"
           title="Fechar">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 6L6 18"></path>
                <path d="M6 6l12 12"></path>
            </svg>
        </a>
    </div>

    <div class="mt-8 rounded-2xl border border-stone-200 bg-white p-6 shadow-card">
        @include('admin.machines._form', [
            'mode' => 'create',
            'machine' => $machine ?? null,
            'categories' => $categories ?? [],
        ])
    </div>
</div>
@endsection
