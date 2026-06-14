@extends('layouts.site')

@section('content')
@php
    $name = $machine->name ?? '—';
    $priceText = $machine->price_formatted;
    $state = $machine->priceState();
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

    $specs = collect([
        ['label' => 'Categoria', 'value' => $categoryName],
        ['label' => 'Marca', 'value' => $machine->brand ?? null],
        ['label' => 'Modelo', 'value' => $machine->model ?? null],
        ['label' => 'Estado', 'value' => $statusLabel],
    ])->filter(fn ($item) => filled($item['value']))->values();
@endphp

<div class="mx-auto max-w-7xl space-y-8 px-4 py-8 sm:px-6 lg:px-8">
    <nav aria-label="Breadcrumb" class="text-sm text-slate-500">
        <ol class="flex flex-wrap items-center gap-2">
            <li>
                <a href="{{ route('site.home') }}" class="rounded transition hover:text-slate-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2">Home</a>
            </li>
            <li class="text-slate-300">/</li>
            <li>
                <a href="{{ route('site.catalog') }}" class="rounded transition hover:text-slate-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2">Catálogo</a>
            </li>
            <li class="text-slate-300">/</li>
            <li class="font-medium text-slate-700">{{ $name }}</li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
        <section class="space-y-6 lg:col-span-7">
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="aspect-[4/3] bg-slate-100">
                    @if($mainUrl)
                        <img id="machine-main-image" src="{{ $mainUrl }}" alt="{{ $name }}" width="1200" height="900" loading="eager" fetchpriority="high" decoding="async" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full w-full items-center justify-center text-slate-400">
                            <svg class="h-14 w-14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                                <path d="M3 16l5-5 4 4 3-3 6 6"></path>
                                <path d="M14 8h.01"></path>
                            </svg>
                        </div>
                    @endif
                </div>
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
                            class="group aspect-square overflow-hidden rounded-xl bg-slate-100 ring-1 ring-slate-200 transition hover:ring-slate-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2"
                            aria-label="Ver imagem adicional"
                        >
                            <img src="{{ $thumbUrl }}" alt="" width="200" height="200" loading="lazy" decoding="async" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]">
                        </button>
                    @endforeach
                </div>
            @endif

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-7">
                <h2 class="text-xl font-semibold tracking-tight text-slate-900">Descrição</h2>
                @if(!empty($machine->description))
                    <div class="mt-3 whitespace-pre-line text-sm leading-relaxed text-slate-700 sm:text-base">
                        {{ $machine->description }}
                    </div>
                @else
                    <p class="mt-3 text-sm text-slate-500">Sem descrição disponível para esta máquina.</p>
                @endif

                @if($specs->isNotEmpty())
                    <div class="mt-8 border-t border-slate-100 pt-6">
                        <h3 class="text-base font-semibold text-slate-900">Especificações</h3>
                        <dl class="mt-3 divide-y divide-slate-100">
                            @foreach($specs as $spec)
                                <div class="flex items-start justify-between gap-4 py-3 text-sm">
                                    <dt class="text-slate-500">{{ $spec['label'] }}</dt>
                                    <dd class="text-right font-medium text-slate-900">{{ $spec['value'] }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                @endif

                <a href="{{ route('site.catalog') }}"
                   class="mt-6 inline-flex rounded text-sm font-medium text-slate-700 underline-offset-4 transition hover:text-slate-900 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2">
                    Ver mais máquinas no catálogo
                </a>
            </div>
        </section>

        <aside class="lg:col-span-5">
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 lg:sticky lg:top-24 sm:p-7">
                <h1 class="text-3xl font-bold tracking-tight text-slate-900">{{ $name }}</h1>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                        {{ $statusLabel }}
                    </span>

                    @if($categoryName)
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                            {{ $categoryName }}
                        </span>
                    @endif

                    @if(isset($machine->negotiable) && $machine->negotiable)
                        <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                            Negociável
                        </span>
                    @endif
                </div>

                <div class="mt-6 border-t border-slate-100 pt-6">
                    @if($state === 'price' || $state === 'price_negotiable')
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Preço estimado</p>
                        <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">{{ $priceText }}</p>
                        @if($state === 'price_negotiable')
                            <p class="mt-2 text-sm text-slate-600">Valor sujeito a negociação.</p>
                        @endif
                    @elseif($state === 'negotiable')
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Preço</p>
                        <p class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Preço negociável</p>
                        <p class="mt-2 text-sm text-slate-600">Contacte-nos para proposta personalizada.</p>
                    @else
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Preço</p>
                        <p class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Sob consulta</p>
                        <p class="mt-2 text-sm text-slate-600">Contacte-nos para proposta personalizada.</p>
                    @endif
                </div>

                <div class="mt-8">
                    <x-whatsapp-button
                        :number="$contactWhatsapp"
                        :message="'Olá! Tenho interesse na máquina ' . $whatsappMachineName . '. Pode dar mais informações?'"
                        label="Contactar no WhatsApp"
                        class="inline-flex w-full items-center justify-center rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-700 focus-visible:ring-offset-2"
                    />

                    <a href="{{ route('site.contact') }}"
                       class="mt-3 inline-flex rounded text-sm font-medium text-slate-700 underline-offset-4 transition hover:text-slate-900 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2">
                        Ver página de contacto
                    </a>
                </div>
            </div>
        </aside>
    </div>
</div>

@if($galleryImages->count() > 1)
<script>
(() => {
    const mainImage = document.getElementById('machine-main-image');
    const thumbs = document.querySelectorAll('[data-gallery-thumb]');

    if (!mainImage || !thumbs.length) {
        return;
    }

    thumbs.forEach((thumb) => {
        thumb.addEventListener('click', () => {
            const src = thumb.getAttribute('data-image-src');
            const alt = thumb.getAttribute('data-image-alt') || mainImage.alt;

            if (!src) {
                return;
            }

            mainImage.src = src;
            mainImage.alt = alt;

            thumbs.forEach((item) => {
                item.classList.remove('ring-2', 'ring-slate-900', 'ring-offset-2');
            });
            thumb.classList.add('ring-2', 'ring-slate-900', 'ring-offset-2');
        });
    });
})();
</script>
@endif
@endsection
