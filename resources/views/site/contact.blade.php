@extends('layouts.site')

@section('content')
@php
    $contactPhone = trim((string) ($siteSettings['contact_phone'] ?? ''));
    $contactEmail = trim((string) ($siteSettings['contact_email'] ?? ''));
    $contactAddress = trim((string) ($siteSettings['contact_address'] ?? ''));
    $contactWhatsapp = trim((string) ($siteSettings['contact_whatsapp'] ?? ''));
    $contactHours = trim((string) ($siteSettings['contact_hours'] ?? ''));

    $phoneHref = preg_replace('/[^\d+]/', '', $contactPhone);
    $phoneHref = is_string($phoneHref) ? $phoneHref : '';

    $whatsappDigits = preg_replace('/\D+/', '', $contactWhatsapp);
    $whatsappDigits = is_string($whatsappDigits) ? $whatsappDigits : '';
    $whatsappHref = $whatsappDigits !== '' ? 'https://wa.me/' . $whatsappDigits : '';

    $hasContactLines = $contactPhone !== '' || $contactEmail !== '' || $contactAddress !== '' || $whatsappHref !== '' || $contactHours !== '';
@endphp

<section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-12">
    <div class="max-w-3xl">
        <p class="inline-flex rounded-full border border-slate-300 bg-white px-3 py-1 text-xs font-medium uppercase tracking-wide text-slate-700">
            Contacto comercial
        </p>
        <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Fale connosco</h1>
        <p class="mt-3 text-sm leading-relaxed text-slate-600 sm:text-base">
            Estamos disponíveis para ajudar na escolha da máquina, condições de entrega e apoio técnico.
        </p>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-12">
        <div class="space-y-4 lg:col-span-7">
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-7">
                <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Telefone</dt>
                        <dd class="mt-2 text-sm text-slate-700">
                            @if($contactPhone !== '' && $phoneHref !== '')
                                <a href="tel:{{ $phoneHref }}" class="font-medium text-slate-900 transition hover:text-slate-700">{{ $contactPhone }}</a>
                            @else
                                Número não configurado.
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Email</dt>
                        <dd class="mt-2 break-all text-sm text-slate-700">
                            @if($contactEmail !== '')
                                <a href="mailto:{{ $contactEmail }}" class="font-medium text-slate-900 transition hover:text-slate-700">{{ $contactEmail }}</a>
                            @else
                                Email não configurado.
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Morada</dt>
                        <dd class="mt-2 whitespace-pre-line text-sm text-slate-700">
                            @if($contactAddress !== '')
                                {{ $contactAddress }}
                            @else
                                Morada não configurada.
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Horário</dt>
                        <dd class="mt-2 whitespace-pre-line text-sm text-slate-700">
                            @if($contactHours !== '')
                                {{ $contactHours }}
                            @else
                                Horário não configurado.
                            @endif
                        </dd>
                    </div>

                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">WhatsApp</dt>
                        <dd class="mt-2 text-sm text-slate-700">
                            @if($whatsappHref !== '')
                                <a href="{{ $whatsappHref }}" target="_blank" rel="noopener" class="font-medium text-slate-900 transition hover:text-slate-700">{{ $whatsappDigits }}</a>
                            @else
                                WhatsApp não configurado.
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-slate-700">
                O formulário de contacto online está em manutenção. Utilize os contactos acima para resposta mais rápida.
            </div>

            @if(!$hasContactLines)
                <div class="rounded-xl border border-dashed border-slate-300 bg-white p-4 text-sm text-slate-600">
                    Os contactos ainda não estão configurados no backoffice.
                </div>
            @endif
        </div>

        <aside class="lg:col-span-5">
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6 shadow-sm lg:sticky lg:top-24 sm:p-7">
                <h2 class="text-xl font-semibold tracking-tight text-slate-900">Resposta rápida por WhatsApp</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-700">
                    Partilhe o modelo de interesse e receba orientação sobre disponibilidade e condições comerciais.
                </p>

                <x-whatsapp-button
                    :number="$contactWhatsapp"
                    message="Olá! Vi o site Maquiveloso e queria mais informações."
                    label="Falar no WhatsApp"
                    class="mt-6 inline-flex w-full items-center justify-center rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-700 focus-visible:ring-offset-2"
                />

                @if($whatsappHref === '')
                    <p class="mt-4 text-sm text-slate-600">WhatsApp ainda não configurado.</p>
                @endif

                <a href="{{ route('site.catalog') }}"
                   class="mt-4 inline-flex rounded text-sm font-medium text-slate-700 underline-offset-4 transition hover:text-slate-900 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2">
                    Ver catálogo
                </a>
            </div>
        </aside>
    </div>
</section>
@endsection
