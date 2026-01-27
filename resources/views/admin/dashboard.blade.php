{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
@php
    use Illuminate\Support\Str;

    // Esperado (ideal): o controller passar estes valores.
    // Fallback seguro para não rebentar:
    $total       = $total       ?? 0;
    $available   = $available   ?? 0;
    $reserved    = $reserved    ?? 0;
    $sold        = $sold        ?? 0;
    $unavailable = $unavailable ?? 0;

    $userName = Auth::check() ? Str::lower(Auth::user()->name) : '';
@endphp

<div class="space-y-8">

    {{-- Header --}}
    <div>
        <h1 class="text-4xl font-extrabold tracking-tight text-gray-900">Dashboard</h1>
    </div>

    {{-- Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
        {{-- Disponíveis --}}
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 flex items-center justify-between">
            <div>
                <div class="text-sm text-gray-500">Disponíveis</div>
                <div class="mt-2 text-4xl font-extrabold text-gray-900">{{ $available }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
                {{-- check icon --}}
                <svg class="w-6 h-6 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 6L9 17l-5-5" />
                </svg>
            </div>
        </div>

        {{-- Reservadas --}}
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 flex items-center justify-between">
            <div>
                <div class="text-sm text-gray-500">Reservadas</div>
                <div class="mt-2 text-4xl font-extrabold text-gray-900">{{ $reserved }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center">
                {{-- clock icon --}}
                <svg class="w-6 h-6 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="9" />
                    <path d="M12 7v6l4 2" />
                </svg>
            </div>
        </div>

        {{-- Vendidas --}}
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 flex items-center justify-between">
            <div>
                <div class="text-sm text-gray-500">Vendidas</div>
                <div class="mt-2 text-4xl font-extrabold text-gray-900">{{ $sold }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                {{-- cart icon --}}
                <svg class="w-6 h-6 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 6h15l-1.5 9h-12z" />
                    <path d="M6 6l-2-2H2" />
                    <circle cx="9" cy="20" r="1" />
                    <circle cx="18" cy="20" r="1" />
                </svg>
            </div>
        </div>

        {{-- Indisponíveis --}}
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 flex items-center justify-between">
            <div>
                <div class="text-sm text-gray-500">Indisponíveis</div>
                <div class="mt-2 text-4xl font-extrabold text-gray-900">{{ $unavailable }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center">
                {{-- x-circle icon --}}
                <svg class="w-6 h-6 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="9" />
                    <path d="M15 9l-6 6" />
                    <path d="M9 9l6 6" />
                </svg>
            </div>
        </div>
    </div>

    {{-- Resumo --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-8">
        <h2 class="text-lg font-semibold text-gray-900">Resumo</h2>

        <div class="mt-4 space-y-2 text-sm text-gray-600">
            <p>Tem um total de <span class="font-semibold text-gray-900">{{ $total }}</span> máquinas registadas no sistema.</p>
            <p>
                Aceda à secção <span class="font-semibold text-gray-900">Máquinas</span> para gerir o catálogo.
            </p>
        </div>

        <div class="mt-6">
            <a href="{{ route('admin.machines.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-900 text-white text-sm font-semibold hover:bg-gray-800">
                Ir para Máquinas
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M5 12h14" />
                    <path d="M13 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>

</div>
@endsection
