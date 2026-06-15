@props([
    'src' => null,
    'alt' => '',
    'ratio' => 'aspect-[4/3]',
    'width' => null,
    'height' => null,
    'lazy' => true,
    'imgClass' => 'h-full w-full object-cover',
])

<div class="relative overflow-hidden bg-stone-100 {{ $ratio }}">
    @if($src)
        <img
            src="{{ $src }}"
            alt="{{ $alt }}"
            @if($width) width="{{ $width }}" @endif
            @if($height) height="{{ $height }}" @endif
            loading="{{ $lazy ? 'lazy' : 'eager' }}"
            decoding="async"
            {{ $attributes->merge(['class' => $imgClass]) }}
        >
    @else
        <div class="flex h-full w-full items-center justify-center text-stone-300">
            <svg class="h-12 w-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
                <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                <path d="M3 16l5-5 4 4 3-3 6 6"></path>
                <circle cx="14" cy="8" r="1.4"></circle>
            </svg>
        </div>
    @endif

    {{ $slot }}
</div>
