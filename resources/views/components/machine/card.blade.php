@props(['machine'])

@php
    $name = $machine->name ?? '—';
    $imgUrl = $machine->main_image?->thumb_url;
    $isPriceNegotiable = $machine->priceState() === 'price_negotiable';
    $categoryName = $machine->category->name ?? null;
@endphp

<a
    href="{{ route('site.machine.show', $machine) }}"
    {{ $attributes->merge(['class' => 'group flex h-full flex-col overflow-hidden rounded-xl border border-slate-200 bg-white transition duration-200 hover:border-navy-300 hover:shadow-[0_10px_30px_-18px_rgba(16,32,58,0.35)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-navy-700 focus-visible:ring-offset-2']) }}
>
    <div class="relative aspect-[4/3] overflow-hidden bg-slate-50">
        @if($imgUrl)
            <img
                src="{{ $imgUrl }}"
                alt="{{ $name }}"
                width="600"
                height="450"
                loading="lazy"
                decoding="async"
                class="h-full w-full object-contain p-5 mix-blend-multiply transition duration-300 motion-safe:group-hover:scale-[1.03]"
            >
        @else
            <div class="flex h-full w-full items-center justify-center text-slate-300">
                <svg class="h-12 w-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
                    <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                    <path d="M3 16l5-5 4 4 3-3 6 6"></path>
                    <circle cx="14" cy="8" r="1.4"></circle>
                </svg>
            </div>
        @endif

        @if($isPriceNegotiable)
            <span class="absolute left-3 top-3 rounded-md bg-white px-2 py-1 text-xs font-semibold text-navy-800 ring-1 ring-inset ring-navy-100">Negociável</span>
        @endif
    </div>

    <div class="flex flex-1 flex-col border-t border-slate-100 p-5">
        @if($categoryName)
            <p class="text-xs font-medium text-slate-500">{{ $categoryName }}</p>
        @endif

        <h3 class="mt-1 line-clamp-2 text-base font-semibold leading-snug text-navy-950">
            {{ $name }}
        </h3>

        <div class="mt-auto flex items-end justify-between gap-3 pt-4">
            <x-machine.price :machine="$machine" variant="card" />

            <span class="shrink-0 text-sm font-semibold text-navy-700 underline-offset-4 group-hover:underline">Ver detalhes</span>
        </div>
    </div>
</a>
