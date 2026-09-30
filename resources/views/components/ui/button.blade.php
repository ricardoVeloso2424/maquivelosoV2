@props([
    'href' => null,
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
])

@php
    $base = 'inline-flex items-center justify-center gap-2 font-semibold rounded-full transition duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed';

    $sizes = [
        'sm' => 'px-4 py-2 text-sm',
        'md' => 'px-5 py-2.5 text-sm',
        'lg' => 'px-7 py-3.5 text-base',
    ];

    $variants = [
        'primary' => 'bg-brand-600 text-white shadow-sm hover:bg-brand-700 hover:shadow-md focus-visible:ring-brand-600',
        'dark'    => 'bg-stone-900 text-white shadow-sm hover:bg-stone-800 focus-visible:ring-stone-900',
        'outline' => 'border border-stone-300 bg-white text-stone-800 hover:border-stone-900 hover:text-stone-900 focus-visible:ring-stone-900',
        'light'   => 'bg-white text-stone-900 shadow-sm hover:bg-stone-100 focus-visible:ring-white',
        'on-dark' => 'border border-white/25 text-white hover:bg-white/10 focus-visible:ring-white focus-visible:ring-offset-stone-950',
        'ghost'   => 'text-stone-700 hover:text-brand-700 focus-visible:ring-brand-600',
        'danger'  => 'bg-red-600 text-white shadow-sm hover:bg-red-700 focus-visible:ring-red-600',
        'danger-outline' => 'border border-red-200 bg-white text-red-600 hover:border-red-300 hover:bg-red-50 focus-visible:ring-red-500',
    ];

    $classes = $base . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
