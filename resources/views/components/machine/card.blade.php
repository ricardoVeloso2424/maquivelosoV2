@props(['machine'])

@php
    $name = $machine->name ?? '—';
    $imgUrl = $machine->main_image?->thumb_url;
    $state = $machine->priceState();
    $showNegotiable = in_array($state, ['price_negotiable', 'negotiable'], true);
    $categoryName = $machine->category->name ?? null;
@endphp

<a
    href="{{ route('site.machine.show', $machine) }}"
    {{ $attributes->merge(['class' => 'group relative flex flex-col overflow-hidden rounded-3xl bg-white shadow-card ring-1 ring-stone-200/70 transition duration-300 hover:-translate-y-1 hover:shadow-card-hover focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2']) }}
>
    <x-ui.image
        :src="$imgUrl"
        :alt="$name"
        ratio="aspect-[4/3]"
        width="500"
        height="375"
        imgClass="h-full w-full object-cover transition duration-500 group-hover:scale-105"
    >
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-stone-900/15 via-transparent to-transparent opacity-0 transition duration-300 group-hover:opacity-100"></div>

        @if($showNegotiable)
            <span class="absolute left-3 top-3">
                <x-ui.badge variant="solid">Negociável</x-ui.badge>
            </span>
        @endif

        @if($categoryName)
            <span class="absolute right-3 top-3 rounded-full bg-white/90 px-2.5 py-1 text-[11px] font-semibold text-stone-700 shadow-sm backdrop-blur">
                {{ $categoryName }}
            </span>
        @endif
    </x-ui.image>

    <div class="flex flex-1 flex-col p-5">
        <h3 class="line-clamp-1 text-lg font-semibold text-stone-900 transition-colors duration-200 group-hover:text-brand-700">
            {{ $name }}
        </h3>

        <div class="mt-3 flex items-end justify-between gap-3">
            <div class="flex min-h-[2.5rem] flex-col justify-center">
                <x-machine.price :machine="$machine" variant="card" />
            </div>

            <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-stone-100 text-stone-600 transition duration-300 group-hover:bg-brand-600 group-hover:text-white">
                <svg class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M5 12h14"></path>
                    <path d="M13 5l7 7-7 7"></path>
                </svg>
            </span>
        </div>
    </div>
</a>
