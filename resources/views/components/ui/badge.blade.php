@props(['variant' => 'neutral'])

@php
    $variants = [
        'neutral'    => 'bg-stone-100 text-stone-700 ring-1 ring-inset ring-stone-200',
        'available'  => 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200',
        'reserved'   => 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200',
        'sold'       => 'bg-sky-50 text-sky-700 ring-1 ring-inset ring-sky-200',
        'inactive'   => 'bg-stone-100 text-stone-500 ring-1 ring-inset ring-stone-200',
        'negotiable' => 'bg-brand-50 text-brand-800 ring-1 ring-inset ring-brand-200',
        'solid'      => 'bg-brand-600 text-white shadow-sm',
    ];

    $classes = 'inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold ' . ($variants[$variant] ?? $variants['neutral']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</span>
