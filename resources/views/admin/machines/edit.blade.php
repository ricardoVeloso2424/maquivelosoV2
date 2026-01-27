@extends('layouts.admin')

@section('title', 'Editar Máquina')

@section('content')
<div class="max-w-4xl">
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">Editar Máquina</h1>
        </div>

        <a href="{{ route('admin.machines.index') }}"
           class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-800 hover:bg-gray-50"
           title="Fechar">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 6L6 18"></path>
                <path d="M6 6l12 12"></path>
            </svg>
        </a>
    </div>

    <div class="mt-8 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        @include('admin.machines._form', [
            'mode' => 'edit',
            'machine' => $machine,
            'categories' => $categories ?? [],
        ])
    </div>
</div>
@endsection
