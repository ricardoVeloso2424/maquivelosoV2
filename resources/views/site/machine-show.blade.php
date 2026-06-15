@extends('layouts.site')

@section('content')
@php
    $name = $machine->name ?? '—';
    $categoryName = $machine->category->name ?? null;

    $images = $machine->images ?? collect();
    $main = $machine->main_image;
    $mainUrl = $main?->public_url;

    $galleryImages = $images->filter(fn ($img) => !empty($img->public_url));
    if ($galleryImages->isEmpty() && $mainUrl) {
        $galleryImages = collect([$main]);
    }

    $contactWhatsapp = trim((string) ($siteSettings['contact_whatsapp'] ?? ''));
    $whatsappMachineName = trim((string) ($machine->name ?? 'máquina'));

    $statusLabel = $machine->status_label;
    $statusVariant = in_array($machine->status, ['available', 'reserved', 'sold', 'inactive'], true)
        ? $machine->status
        : 'neutral';

    $specs = collect([
        ['label' => 'Categoria', 'value' => $categoryName],
        ['label' => 'Marca', 'value' => $machine->brand ?? null],
        ['label' => 'Modelo', 'value' => $machine->model ?? null],
        ['label' => 'Estado', 'value' => $statusLabel],
    ])->filter(fn ($item) => filled($item['value']))->values();
@endphp

<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    {{-- Breadcrumb --}}
    <nav aria-label="Breadcrumb" class="text-sm text-stone-500">
        <ol class="flex flex-wrap items-center gap-2">
            <li><a href="{{ route('site.home') }}" class="rounded transition hover:text-stone-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2">Início</a></li>
            <li class="text-stone-300">/</li>
            <li><a href="{{ route('site.catalog') }}" class="rounded transition hover:text-stone-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2">Catálogo</a></li>
            <li class="text-stone-300">/</li>
            <li class="font-medium text-stone-700">{{ $name }}</li>
        </ol>
    </nav>

    <div class="mt-6 grid grid-cols-1 gap-8 lg:grid-cols-12">
        {{-- Gallery --}}
        <section class="space-y-4 lg:col-span-7">
            <div class="overflow-hidden rounded-3xl bg-white shadow-card ring-1 ring-stone-200/70">
                @if($mainUrl)
                    <button type="button" data-zoom-trigger class="group relative block w-full cursor-zoom-in" aria-label="Ampliar imagem">
                        <div class="relative aspect-[4/3] overflow-hidden bg-stone-100">
                            <img
                                id="machine-main-image"
                                src="{{ $mainUrl }}"
                                alt="{{ $name }}"
                                width="1200"
                                height="900"
                                loading="eager"
                                fetchpriority="high"
                                decoding="async"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.02]"
                            >
                            <span class="absolute bottom-3 right-3 inline-flex items-center gap-1.5 rounded-full bg-stone-900/70 px-3 py-1.5 text-xs font-semibold text-white opacity-0 backdrop-blur transition group-hover:opacity-100">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="M21 21l-4.3-4.3"></path><path d="M11 8v6"></path><path d="M8 11h6"></path></svg>
                                Ampliar
                            </span>
                        </div>
                    </button>
                @else
                    <div class="flex aspect-[4/3] w-full items-center justify-center bg-stone-100 text-stone-300">
                        <svg class="h-16 w-16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M3 16l5-5 4 4 3-3 6 6"></path><circle cx="14" cy="8" r="1.4"></circle></svg>
                    </div>
                @endif
            </div>

            @if($galleryImages->count() > 1)
                <div class="grid grid-cols-4 gap-3 sm:grid-cols-6">
                    @foreach($galleryImages as $img)
                        @php
                            $thumbUrl = $img->thumb_url;
                            $fullUrl = $img->public_url;
                        @endphp
                        <button
                            type="button"
                            data-gallery-thumb
                            data-image-src="{{ $fullUrl }}"
                            data-image-alt="{{ $name }}"
                            class="group aspect-square overflow-hidden rounded-2xl bg-stone-100 ring-1 ring-stone-200 transition hover:ring-brand-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2"
                            aria-label="Ver imagem adicional"
                        >
                            <img src="{{ $thumbUrl }}" alt="" width="200" height="200" loading="lazy" decoding="async" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                        </button>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Info panel --}}
        <aside class="lg:col-span-5">
            <div class="lg:sticky lg:top-28">
                <div class="rounded-3xl bg-white p-6 shadow-card ring-1 ring-stone-200/70 sm:p-8">
                    <div class="flex flex-wrap items-center gap-2">
                        <x-ui.badge :variant="$statusVariant">
                            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $statusLabel }}
                        </x-ui.badge>
                        @if($categoryName)
                            <x-ui.badge variant="neutral">{{ $categoryName }}</x-ui.badge>
                        @endif
                        @if($machine->negotiable)
                            <x-ui.badge variant="negotiable">Negociável</x-ui.badge>
                        @endif
                    </div>

                    <h1 class="mt-4 font-serif text-3xl font-semibold tracking-tight text-stone-900 sm:text-4xl">{{ $name }}</h1>

                    <div class="mt-6 border-t border-stone-100 pt-6">
                        <x-machine.price :machine="$machine" variant="detail" />
                    </div>

                    <div class="mt-8 space-y-3">
                        <x-whatsapp-button
                            :number="$contactWhatsapp"
                            :message="'Olá! Tenho interesse na máquina ' . $whatsappMachineName . '. Pode dar mais informações?'"
                            label="Contactar no WhatsApp"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-emerald-600 px-5 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-700 focus-visible:ring-offset-2"
                        />

                        <x-ui.button :href="route('site.contact')" variant="outline" size="lg" class="w-full">
                            Ver página de contacto
                        </x-ui.button>
                    </div>
                </div>

                @if($specs->isNotEmpty())
                    <div class="mt-4 rounded-3xl bg-white p-6 shadow-card ring-1 ring-stone-200/70 sm:p-8">
                        <h2 class="text-sm font-semibold uppercase tracking-[0.18em] text-stone-500">Especificações</h2>
                        <dl class="mt-4 divide-y divide-stone-100">
                            @foreach($specs as $spec)
                                <div class="flex items-start justify-between gap-4 py-3 text-sm">
                                    <dt class="text-stone-500">{{ $spec['label'] }}</dt>
                                    <dd class="text-right font-medium text-stone-900">{{ $spec['value'] }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                @endif
            </div>
        </aside>
    </div>

    {{-- Description --}}
    <section class="mt-8 rounded-3xl bg-white p-6 shadow-card ring-1 ring-stone-200/70 sm:p-8 lg:max-w-3xl">
        <div class="flex items-center gap-3">
            <span class="h-px w-7 stitch-line text-brand-500"></span>
            <h2 class="font-serif text-2xl font-semibold tracking-tight text-stone-900">Descrição</h2>
        </div>
        @if(!empty($machine->description))
            <div class="mt-4 whitespace-pre-line text-sm leading-relaxed text-stone-700 sm:text-base">{{ $machine->description }}</div>
        @else
            <p class="mt-4 text-sm text-stone-500">Sem descrição disponível para esta máquina.</p>
        @endif

        <a href="{{ route('site.catalog') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-brand-700 transition hover:text-brand-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 12H5"></path><path d="M11 5l-7 7 7 7"></path></svg>
            Ver mais máquinas no catálogo
        </a>
    </section>
</div>

@if($mainUrl)
    {{-- Lightbox --}}
    <div id="image-lightbox" class="fixed inset-0 z-[70] hidden items-center justify-center bg-stone-950/90 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-label="Imagem ampliada">
        <button type="button" data-lightbox-close class="absolute right-4 top-4 inline-flex h-11 w-11 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white" aria-label="Fechar">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 6l12 12"></path><path d="M18 6 6 18"></path></svg>
        </button>
        <img id="lightbox-image" src="" alt="{{ $name }}" class="max-h-[90vh] max-w-full rounded-2xl object-contain shadow-2xl">
    </div>
@endif

<script>
(() => {
    const mainImage = document.getElementById('machine-main-image');

    // Thumbnail → main image swap
    const thumbs = document.querySelectorAll('[data-gallery-thumb]');
    if (mainImage && thumbs.length) {
        thumbs.forEach((thumb) => {
            thumb.addEventListener('click', () => {
                const src = thumb.getAttribute('data-image-src');
                const alt = thumb.getAttribute('data-image-alt') || mainImage.alt;
                if (!src) return;

                mainImage.src = src;
                mainImage.alt = alt;

                thumbs.forEach((item) => item.classList.remove('ring-2', 'ring-brand-600', 'ring-offset-2'));
                thumb.classList.add('ring-2', 'ring-brand-600', 'ring-offset-2');
            });
        });
    }

    // Lightbox (zoom)
    const lightbox = document.getElementById('image-lightbox');
    const lightboxImage = document.getElementById('lightbox-image');
    const trigger = document.querySelector('[data-zoom-trigger]');

    if (lightbox && lightboxImage && trigger && mainImage) {
        const open = () => {
            lightboxImage.src = mainImage.src;
            lightboxImage.alt = mainImage.alt;
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
            document.body.style.overflow = 'hidden';
        };
        const close = () => {
            lightbox.classList.add('hidden');
            lightbox.classList.remove('flex');
            document.body.style.overflow = '';
        };

        trigger.addEventListener('click', open);
        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox || e.target.closest('[data-lightbox-close]')) close();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !lightbox.classList.contains('hidden')) close();
        });
    }
})();
</script>
@endsection
