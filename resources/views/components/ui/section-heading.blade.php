@props([
    'eyebrow' => null,
    'title' => '',
    'align' => 'left',
    'invert' => false,
])

@php
    $center = $align === 'center';
    $titleColor = $invert ? 'text-white' : 'text-stone-900';
    $subColor = $invert ? 'text-stone-300' : 'text-stone-600';
    $eyebrowColor = $invert ? 'text-brand-300' : 'text-brand-700';
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col gap-3 ' . ($center ? 'text-center items-center' : 'items-start')]) }}>
    @if($eyebrow)
        <span class="inline-flex items-center gap-2.5 text-xs font-semibold uppercase tracking-[0.2em] {{ $eyebrowColor }}">
            <span class="h-px w-7 stitch-line"></span>{{ $eyebrow }}
        </span>
    @endif

    <h2 class="font-serif text-3xl font-semibold tracking-tight {{ $titleColor }} sm:text-4xl">{{ $title }}</h2>

    @if($slot->isNotEmpty())
        <p class="max-w-2xl text-base leading-relaxed {{ $subColor }}">{{ $slot }}</p>
    @endif
</div>
