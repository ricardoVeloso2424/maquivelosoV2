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

{{-- Header --}}
<section class="relative overflow-hidden border-b border-stone-200 bg-white">
    <div class="pattern-dots pointer-events-none absolute inset-0 opacity-60"></div>
    <div class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        <div class="max-w-3xl">
            <span class="inline-flex items-center gap-2.5 text-xs font-semibold uppercase tracking-[0.2em] text-brand-700">
                <span class="h-px w-7 stitch-line"></span>Contacto comercial
            </span>
            <h1 class="mt-4 font-serif text-4xl font-semibold tracking-tight text-stone-900 sm:text-5xl">Fale connosco</h1>
            <p class="mt-4 text-base leading-relaxed text-stone-600 sm:text-lg">
                Estamos disponíveis para ajudar na escolha da máquina, condições de entrega e apoio técnico.
            </p>
        </div>
    </div>
</section>

<div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
        {{-- Contact details --}}
        <div class="lg:col-span-7" data-reveal>
            <div class="rounded-3xl bg-white p-6 shadow-card ring-1 ring-stone-200/70 sm:p-8">
                <h2 class="font-serif text-2xl font-semibold tracking-tight text-stone-900">Os nossos contactos</h2>

                @if($hasContactLines)
                    <dl class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2">
                        @if($contactPhone !== '')
                            <div class="flex items-start gap-4 rounded-2xl bg-stone-50 p-4 ring-1 ring-stone-100">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-700"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.9.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"></path></svg></span>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-stone-500">Telefone</dt>
                                    <dd class="mt-1 text-sm">
                                        @if($phoneHref !== '')
                                            <a href="tel:{{ $phoneHref }}" class="font-semibold text-stone-900 transition hover:text-brand-700">{{ $contactPhone }}</a>
                                        @else
                                            <span class="font-semibold text-stone-900">{{ $contactPhone }}</span>
                                        @endif
                                    </dd>
                                </div>
                            </div>
                        @endif

                        @if($contactEmail !== '')
                            <div class="flex items-start gap-4 rounded-2xl bg-stone-50 p-4 ring-1 ring-stone-100">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-700"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m3 7 9 6 9-6"></path></svg></span>
                                <div class="min-w-0">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-stone-500">Email</dt>
                                    <dd class="mt-1 break-all text-sm"><a href="mailto:{{ $contactEmail }}" class="font-semibold text-stone-900 transition hover:text-brand-700">{{ $contactEmail }}</a></dd>
                                </div>
                            </div>
                        @endif

                        @if($contactAddress !== '')
                            <div class="flex items-start gap-4 rounded-2xl bg-stone-50 p-4 ring-1 ring-stone-100">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-700"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg></span>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-stone-500">Morada</dt>
                                    <dd class="mt-1 whitespace-pre-line text-sm font-medium text-stone-900">{{ $contactAddress }}</dd>
                                </div>
                            </div>
                        @endif

                        @if($contactHours !== '')
                            <div class="flex items-start gap-4 rounded-2xl bg-stone-50 p-4 ring-1 ring-stone-100">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-700"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v6l4 2"></path></svg></span>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-stone-500">Horário</dt>
                                    <dd class="mt-1 whitespace-pre-line text-sm font-medium text-stone-900">{{ $contactHours }}</dd>
                                </div>
                            </div>
                        @endif

                        @if($whatsappHref !== '')
                            <div class="flex items-start gap-4 rounded-2xl bg-stone-50 p-4 ring-1 ring-stone-100">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15l-1.3 4.7 4.8-1.3A10 10 0 1 0 12 2zm0 18a8 8 0 0 1-4.1-1.1l-.3-.2-2.8.8.8-2.8-.2-.3A8 8 0 1 1 12 20zm4.4-5.6c-.2-.1-1.4-.7-1.6-.8-.2-.1-.4-.1-.6.1-.2.2-.6.8-.8 1-.1.1-.3.2-.5.1a6.5 6.5 0 0 1-3.2-2.8c-.2-.4.2-.4.6-1.2.1-.1 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3a3 3 0 0 0-.9 2.2c0 1.3 1 2.6 1.1 2.7.1.2 1.9 2.9 4.6 4 .6.3 1.1.4 1.5.5.6.2 1.2.2 1.6.1.5-.1 1.4-.6 1.6-1.1.2-.6.2-1 .1-1.1z"></path></svg></span>
                                <div class="min-w-0">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-stone-500">WhatsApp</dt>
                                    <dd class="mt-1 break-all text-sm"><a href="{{ $whatsappHref }}" target="_blank" rel="noopener" class="font-semibold text-stone-900 transition hover:text-emerald-700">{{ $whatsappDigits }}</a></dd>
                                </div>
                            </div>
                        @endif
                    </dl>
                @else
                    <div class="mt-6 rounded-2xl border border-dashed border-stone-300 bg-stone-50 p-6 text-sm text-stone-600">
                        Os contactos ainda não estão configurados no backoffice.
                    </div>
                @endif
            </div>
        </div>

        {{-- Quick contact --}}
        <aside class="lg:col-span-5" data-reveal data-reveal-delay="2">
            <div class="overflow-hidden rounded-3xl bg-stone-950 text-white shadow-card lg:sticky lg:top-28">
                <div class="relative p-6 sm:p-8">
                    <div class="pattern-dots-light pointer-events-none absolute inset-0 opacity-40"></div>
                    <div class="relative">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-500/15 text-emerald-400 ring-1 ring-emerald-500/20">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15l-1.3 4.7 4.8-1.3A10 10 0 1 0 12 2z"></path></svg>
                        </span>
                        <h2 class="mt-5 font-serif text-2xl font-semibold">Resposta rápida</h2>

                        @if($whatsappHref !== '')
                            <p class="mt-2 text-sm leading-relaxed text-stone-300">
                                Escreva a sua mensagem e abrimos o WhatsApp com tudo pronto a enviar.
                            </p>

                            <form data-wa-form class="mt-6 space-y-4">
                                <div>
                                    <label for="wa_nome" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-stone-400">O seu nome</label>
                                    <input id="wa_nome" name="nome" type="text" autocomplete="name" placeholder="Nome" class="w-full rounded-xl border-0 bg-white/10 px-4 py-3 text-sm text-white placeholder:text-stone-500 focus:bg-white/15 focus:ring-2 focus:ring-emerald-500">
                                </div>
                                <div>
                                    <label for="wa_msg" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-stone-400">Mensagem</label>
                                    <textarea id="wa_msg" name="mensagem" rows="3" placeholder="Em que podemos ajudar?" class="w-full rounded-xl border-0 bg-white/10 px-4 py-3 text-sm text-white placeholder:text-stone-500 focus:bg-white/15 focus:ring-2 focus:ring-emerald-500"></textarea>
                                </div>
                                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-emerald-600 px-5 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-950">
                                    Enviar pelo WhatsApp
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14"></path><path d="M13 5l7 7-7 7"></path></svg>
                                </button>
                            </form>

                            <div class="my-6 h-px w-full stitch-line text-white/15"></div>

                            <x-whatsapp-button
                                :number="$contactWhatsapp"
                                message="Olá! Vi o site Maquiveloso e queria mais informações."
                                label="Falar no WhatsApp"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-full border border-white/25 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-stone-950"
                            />
                        @else
                            <p class="mt-2 text-sm leading-relaxed text-stone-300">
                                Utilize os contactos ao lado para falar connosco. Teremos todo o gosto em ajudar.
                            </p>
                            <div class="mt-6 space-y-3">
                                @if($phoneHref !== '')
                                    <a href="tel:{{ $phoneHref }}" class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-white px-5 py-3 text-sm font-semibold text-stone-900 transition hover:bg-stone-100">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.9.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                        Ligar agora
                                    </a>
                                @endif
                                @if($contactEmail !== '')
                                    <a href="mailto:{{ $contactEmail }}" class="inline-flex w-full items-center justify-center gap-2 rounded-full border border-white/25 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m3 7 9 6 9-6"></path></svg>
                                        Enviar email
                                    </a>
                                @endif
                            </div>
                        @endif

                        <a href="{{ route('site.catalog') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-stone-300 transition hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-stone-950">
                            Ver catálogo
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14"></path><path d="M13 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>

@if($whatsappHref !== '')
<script>
(() => {
    const form = document.querySelector('[data-wa-form]');
    if (!form) return;

    const base = "{{ $whatsappHref }}";

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const nome = (form.querySelector('[name="nome"]').value || '').trim();
        const msg = (form.querySelector('[name="mensagem"]').value || '').trim();
        const text = `Olá! ${nome ? 'Sou ' + nome + '. ' : ''}${msg || 'Gostaria de mais informações.'}`;
        window.open(base + '?text=' + encodeURIComponent(text), '_blank', 'noopener');
    });
})();
</script>
@endif
@endsection
