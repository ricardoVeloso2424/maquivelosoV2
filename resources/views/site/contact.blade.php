@extends('layouts.site')

@section('content')
@php
    // Same fields (and legacy fallbacks) the backoffice saves and the header/footer read.
    $contactPhone = trim((string) (($siteSettings['contact_phone'] ?? '') ?: ($siteSettings['phone'] ?? '')));
    $contactEmail = trim((string) (($siteSettings['contact_email'] ?? '') ?: ($siteSettings['email'] ?? '')));
    $contactAddress = trim((string) (($siteSettings['contact_address'] ?? '') ?: ($siteSettings['location'] ?? '')));
    $contactWhatsapp = trim((string) ($siteSettings['contact_whatsapp'] ?? ''));
    $contactHours = trim((string) ($siteSettings['contact_hours'] ?? ''));

    $phoneHref = preg_replace('/[^\d+]/', '', $contactPhone);
    $phoneHref = is_string($phoneHref) ? $phoneHref : '';

    $whatsappDigits = preg_replace('/\D+/', '', $contactWhatsapp);
    $whatsappDigits = is_string($whatsappDigits) ? $whatsappDigits : '';
    $whatsappHref = $whatsappDigits !== '' ? 'https://wa.me/' . $whatsappDigits : '';

    // Navigation links are built from the configured address (no stored coordinates exist).
    $addressQuery = preg_replace('/\s*\R\s*/', ', ', $contactAddress);
    $addressQuery = is_string($addressQuery) ? $addressQuery : $contactAddress;
    $googleMapsHref = $contactAddress !== '' ? 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode($addressQuery) : '';
    $wazeHref = $contactAddress !== '' ? 'https://waze.com/ul?q=' . rawurlencode($addressQuery) . '&navigate=yes' : '';

    $hasContactLines = $contactPhone !== '' || $contactEmail !== '' || $whatsappHref !== '' || $contactHours !== '';

    $iconClass = 'mt-0.5 h-5 w-5 shrink-0 text-navy-600';
    $labelClass = 'text-sm text-slate-500';
    $valueLinkClass = 'rounded text-lg font-semibold text-navy-950 underline-offset-4 hover:text-navy-700 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-navy-700 focus-visible:ring-offset-2';
    $navButtonClass = 'inline-flex min-h-11 items-center justify-center rounded-lg bg-white px-5 text-sm font-semibold text-navy-950 transition-colors hover:bg-navy-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-navy-900';
@endphp

<section class="bg-[#e6ecf2]" aria-labelledby="contact-title">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        <h1 id="contact-title" class="text-4xl font-semibold tracking-tight text-navy-950 sm:text-5xl">Contacto</h1>
        <p class="mt-3 max-w-2xl text-lg leading-relaxed text-slate-600">
            Estamos disponíveis para ajudar na escolha da máquina, condições de entrega e apoio técnico.
        </p>

        @if($hasContactLines || $contactAddress !== '')
            <div class="mt-8 grid grid-cols-1 gap-6 lg:mt-10 lg:grid-cols-12 lg:items-stretch lg:gap-8">
                @if($hasContactLines)
                    <div class="rounded-xl border border-slate-300/70 bg-white p-6 sm:p-8 {{ $contactAddress !== '' ? 'lg:col-span-7' : 'lg:col-span-12' }}">
                        <h2 class="text-xl font-semibold tracking-tight text-navy-950">Os nossos contactos</h2>

                        <dl class="mt-4 divide-y divide-slate-200/80">
                            @if($contactPhone !== '')
                                <div class="flex items-start gap-4 py-4">
                                    <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.9.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                    <div class="min-w-0">
                                        <dt class="{{ $labelClass }}">Telefone</dt>
                                        <dd class="mt-0.5">
                                            @if($phoneHref !== '')
                                                <a href="tel:{{ $phoneHref }}" class="{{ $valueLinkClass }}">{{ $contactPhone }}</a>
                                            @else
                                                <span class="text-lg font-semibold text-navy-950">{{ $contactPhone }}</span>
                                            @endif
                                        </dd>
                                    </div>
                                </div>
                            @endif

                            @if($contactEmail !== '')
                                <div class="flex items-start gap-4 py-4">
                                    <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m3 7 9 6 9-6"></path></svg>
                                    <div class="min-w-0">
                                        <dt class="{{ $labelClass }}">Email</dt>
                                        <dd class="mt-0.5 break-all"><a href="mailto:{{ $contactEmail }}" class="{{ $valueLinkClass }}">{{ $contactEmail }}</a></dd>
                                    </div>
                                </div>
                            @endif

                            @if($whatsappHref !== '')
                                <div class="flex items-start gap-4 py-4">
                                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15l-1.3 4.7 4.8-1.3A10 10 0 1 0 12 2zm0 18a8 8 0 0 1-4.1-1.1l-.3-.2-2.8.8.8-2.8-.2-.3A8 8 0 1 1 12 20zm4.4-5.6c-.2-.1-1.4-.7-1.6-.8-.2-.1-.4-.1-.6.1-.2.2-.6.8-.8 1-.1.1-.3.2-.5.1a6.5 6.5 0 0 1-3.2-2.8c-.2-.4.2-.4.6-1.2.1-.1 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3a3 3 0 0 0-.9 2.2c0 1.3 1 2.6 1.1 2.7.1.2 1.9 2.9 4.6 4 .6.3 1.1.4 1.5.5.6.2 1.2.2 1.6.1.5-.1 1.4-.6 1.6-1.1.2-.6.2-1 .1-1.1z"></path></svg>
                                    <div class="min-w-0">
                                        <dt class="{{ $labelClass }}">WhatsApp</dt>
                                        <dd class="mt-0.5 break-all"><a href="{{ $whatsappHref }}" target="_blank" rel="noopener noreferrer" class="{{ $valueLinkClass }}">{{ $whatsappDigits }}</a></dd>
                                    </div>
                                </div>
                            @endif

                            @if($contactHours !== '')
                                <div class="flex items-start gap-4 py-4">
                                    <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v6l4 2"></path></svg>
                                    <div class="min-w-0">
                                        <dt class="{{ $labelClass }}">Horário</dt>
                                        <dd class="mt-0.5 whitespace-pre-line text-base font-semibold leading-relaxed text-navy-950">{{ $contactHours }}</dd>
                                    </div>
                                </div>
                            @endif
                        </dl>
                    </div>
                @endif

                @if($contactAddress !== '')
                    <div class="flex flex-col rounded-xl bg-navy-900 p-6 text-white sm:p-8 {{ $hasContactLines ? 'lg:col-span-5' : 'lg:col-span-6' }}">
                        <div class="flex items-center gap-3">
                            <svg class="h-5 w-5 shrink-0 text-navy-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            <h2 class="text-xl font-semibold tracking-tight">Como chegar</h2>
                        </div>

                        <address class="mt-5 whitespace-pre-line text-lg not-italic leading-relaxed text-navy-100">{{ $contactAddress }}</address>

                        <div class="mt-8 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:mt-auto lg:pt-8">
                            <a href="{{ $googleMapsHref }}" target="_blank" rel="noopener noreferrer" class="{{ $navButtonClass }}">
                                Google Maps
                                <span class="sr-only">(abre numa nova janela)</span>
                            </a>
                            <a href="{{ $wazeHref }}" target="_blank" rel="noopener noreferrer" class="{{ $navButtonClass }}">
                                Waze
                                <span class="sr-only">(abre numa nova janela)</span>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="mt-8 rounded-xl border border-dashed border-slate-400/60 bg-white px-6 py-10 text-center text-sm text-slate-600 lg:mt-10">
                Os contactos ainda não estão configurados no backoffice.
            </div>
        @endif
    </div>
</section>
@endsection
