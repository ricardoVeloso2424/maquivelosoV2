{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
@php
    $total       = $total       ?? 0;
    $available   = $available   ?? 0;
    $reserved    = $reserved    ?? 0;
    $sold        = $sold        ?? 0;
    $unavailable = $unavailable ?? 0;

    $cards = [
        ['label' => 'Disponíveis',   'value' => $available,   'tile' => 'bg-emerald-50 text-emerald-600', 'icon' => 'check'],
        ['label' => 'Reservadas',    'value' => $reserved,    'tile' => 'bg-amber-50 text-amber-600',     'icon' => 'clock'],
        ['label' => 'Vendidas',      'value' => $sold,        'tile' => 'bg-sky-50 text-sky-600',         'icon' => 'cart'],
        ['label' => 'Indisponíveis', 'value' => $unavailable, 'tile' => 'bg-stone-100 text-stone-500',    'icon' => 'x'],
    ];
@endphp

<div class="space-y-8">
    <div>
        <span class="inline-flex items-center gap-2.5 text-xs font-semibold uppercase tracking-[0.2em] text-brand-700">
            <span class="h-px w-7 stitch-line"></span>Gestão
        </span>
        <h1 class="mt-2 font-serif text-4xl font-semibold tracking-tight text-stone-900">Dashboard</h1>
    </div>

    {{-- Cards --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
        @foreach($cards as $card)
            <div class="flex items-center justify-between rounded-2xl border border-stone-200 bg-white p-6 shadow-card">
                <div>
                    <div class="text-sm text-stone-500">{{ $card['label'] }}</div>
                    <div class="mt-2 font-serif text-4xl font-semibold text-stone-900">{{ $card['value'] }}</div>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl {{ $card['tile'] }}">
                    @switch($card['icon'])
                        @case('check')
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                            @break
                        @case('clock')
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v6l4 2"/></svg>
                            @break
                        @case('cart')
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6h15l-1.5 9h-12z"/><path d="M6 6l-2-2H2"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>
                            @break
                        @default
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M15 9l-6 6"/><path d="M9 9l6 6"/></svg>
                    @endswitch
                </div>
            </div>
        @endforeach
    </div>

    {{-- Resumo --}}
    <div class="rounded-2xl border border-stone-200 bg-white p-8 shadow-card">
        <h2 class="font-serif text-xl font-semibold text-stone-900">Resumo</h2>

        <div class="mt-4 space-y-2 text-sm text-stone-600">
            <p>Tem um total de <span class="font-semibold text-stone-900">{{ $total }}</span> máquinas registadas no sistema.</p>
            <p>Aceda à secção <span class="font-semibold text-stone-900">Máquinas</span> para gerir o catálogo.</p>
        </div>

        <div class="mt-6">
            <x-ui.button :href="route('admin.machines.index')" variant="dark" size="md">
                Ir para Máquinas
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14"/><path d="M13 5l7 7-7 7"/></svg>
            </x-ui.button>
        </div>
    </div>
</div>
@endsection
