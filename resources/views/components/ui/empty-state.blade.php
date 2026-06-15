@props(['title' => ''])

<div {{ $attributes->merge(['class' => 'rounded-3xl border border-dashed border-stone-300 bg-white/70 p-10 text-center sm:p-14']) }}>
    <div class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-stone-100 text-stone-400">
        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
            <path d="M3 7l9-4 9 4-9 4-9-4z"></path>
            <path d="M3 7v10l9 4 9-4V7"></path>
            <path d="M12 11v10"></path>
        </svg>
    </div>

    <h3 class="font-serif text-xl font-semibold text-stone-900">{{ $title }}</h3>

    @if($slot->isNotEmpty())
        <p class="mx-auto mt-2 max-w-md text-sm leading-relaxed text-stone-600">{{ $slot }}</p>
    @endif

    @isset($action)
        <div class="mt-6 flex justify-center">{{ $action }}</div>
    @endisset
</div>
