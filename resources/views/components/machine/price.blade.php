@props([
    'machine',
    'variant' => 'card',
])

@php
    $state = $machine->priceState();
    $price = $machine->price_formatted;
@endphp

@if($variant === 'detail')
    <div>
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">Preço</p>

        @if($state === 'price' || $state === 'price_negotiable')
            <p class="mt-2 font-serif text-4xl font-semibold tracking-tight text-stone-900">{{ $price }}</p>
            @if($state === 'price_negotiable')
                <p class="mt-2 text-sm text-stone-600">Valor sujeito a negociação.</p>
            @endif
        @elseif($state === 'negotiable')
            <p class="mt-2 font-serif text-3xl font-semibold tracking-tight text-stone-900">Preço negociável</p>
            <p class="mt-2 text-sm text-stone-600">Contacte-nos para uma proposta personalizada.</p>
        @else
            <p class="mt-2 font-serif text-3xl font-semibold tracking-tight text-stone-900">Sob consulta</p>
            <p class="mt-2 text-sm text-stone-600">Contacte-nos para uma proposta personalizada.</p>
        @endif
    </div>
@else
    @if($state === 'on_request')
        <p class="text-sm font-semibold text-stone-500">Sob consulta</p>
    @elseif($state === 'negotiable')
        <p class="text-sm font-semibold text-brand-700">Preço negociável</p>
    @else
        <p class="font-serif text-2xl font-semibold tracking-tight text-stone-900">{{ $price }}</p>
    @endif
@endif
