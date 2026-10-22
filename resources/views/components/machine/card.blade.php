@props(['machine'])

@php
    $name = $machine->name ?? '—';
    $imgUrl = $machine->main_image?->thumb_url;
    $isPriceNegotiable = $machine->priceState() === 'price_negotiable';
@endphp

<a
    href="{{ route('site.machine.show', $machine) }}"
    {{ $attributes->merge(['class' => 'group flex h-full flex-col overflow-hidden rounded-lg border border-slate-300/80 bg-white transition duration-200 hover:-translate-y-0.5 hover:border-slate-400/80 hover:shadow-[0_8px_24px_-16px_rgba(16,32,58,0.25)] motion-reduce:transition-none motion-reduce:hover:translate-y-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-navy-700 focus-visible:ring-offset-2']) }}
>
    <div class="relative aspect-[4/3] overflow-hidden bg-[#eef1f4]">
        @if($imgUrl)
            <img
                src="{{ $imgUrl }}"
                alt="{{ $name }}"
                width="600"
                height="450"
                loading="lazy"
                decoding="async"
                class="h-full w-full object-contain p-4 mix-blend-multiply transition duration-300 sm:p-5 motion-safe:group-hover:scale-[1.02]"
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
            <span class="absolute left-3 top-3 rounded bg-white px-2 py-0.5 text-xs font-medium text-navy-800 ring-1 ring-inset ring-slate-200">Negociável</span>
        @endif
    </div>

    <div class="flex flex-1 flex-col px-5 pb-4 pt-4">
        <h3 class="mb-4 line-clamp-3 text-[0.9375rem] font-semibold leading-snug text-navy-950">
            {{ $name }}
        </h3>

        <div class="mt-auto flex items-center justify-between gap-3 border-t border-slate-100 pt-3.5">
            <x-machine.price :machine="$machine" variant="card" />

            <span class="inline-flex shrink-0 items-center gap-1.5 text-sm font-semibold text-navy-700 underline-offset-4 group-hover:text-navy-900 group-hover:underline">
                Ver detalhes
                <svg class="h-3.5 w-3.5 transition-transform motion-safe:group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"></path><path d="M13 6l6 6-6 6"></path></svg>
            </span>
        </div>
    </div>
</a>
