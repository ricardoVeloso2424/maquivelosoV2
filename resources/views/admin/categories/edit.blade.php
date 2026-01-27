@extends('layouts.admin')

@section('title', 'Editar Categoria')

@section('content')
<div class="flex items-start justify-between">
    <div>
        <h1 class="text-4xl font-semibold tracking-tight">Editar Categoria</h1>
        <p class="text-gray-500 mt-2">Atualiza o nome da categoria.</p>
    </div>
</div>

<div class="mt-8 bg-white border rounded-2xl p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.categories._form', ['category' => $category])

        <div class="flex items-center gap-3 pt-2">
            <a href="{{ route('admin.categories.index') }}"
               class="px-5 py-3 rounded-xl border border-gray-200 hover:bg-gray-50 font-medium">
                Cancelar
            </a>

            <button class="ml-auto px-6 py-3 rounded-xl bg-gray-900 text-white font-medium hover:bg-gray-800">
                Guardar Alterações
            </button>
        </div>
    </form>
</div>
@endsection
