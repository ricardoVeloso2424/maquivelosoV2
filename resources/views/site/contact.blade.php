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
<section class="bg-white" aria-labelledby="contact-title">
    <div class="mx-auto max-w-7xl px-4 pb-10 pt-12 sm:px-6 sm:pt-16 lg:px-8 lg:pb-12">
        <h1 id="contact-title" class="text-4xl font-semibold tracking-tight text-navy-950 sm:text-5xl">Fale connosco</h1>
        <p class="mt-4 max-w-2xl text-lg leading-relaxed text-slate-600">
            Estamos disponíveis para ajudar na escolha da máquina, condições de entrega e apoio técnico.
        </p>
    </div>
</section>

<section class="bg-white" aria-label="Contactos">
    <div class="mx-auto grid max-w-7xl grid-cols-1 gap-10 px-4 pb-20 sm:px-6 lg:grid-cols-12 lg:gap-12 lg:px-8 lg:pb-24">
        {{-- Contact details --}}
        <div class="lg:col-span-7">
            <h2 class="text-2xl font-semibold tracking-tight text-navy-950">Os nossos contactos</h2>

            @if($hasContactLines)
                <dl class="mt-6 grid grid-cols-1 sm:grid-cols-2 sm:gap-x-10">
                    @if($contactPhone !== '')
                        <div class="flex items-start gap-4 border-t border-slate-200 py-5">
                            <svg class="mt-0.5 h-5 w-5 shrink-0 text-navy-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.9.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            <div>
                                <dt class="text-sm text-slate-500">Telefone</dt>
                                <dd class="mt-1">
                                    @if($phoneHref !== '')
                                        <a href="tel:{{ $phoneHref }}" class="font-semibold text-navy-950 underline-offset-4 hover:underline">{{ $contactPhone }}</a>
                                    @else
                                        <span class="font-semibold text-navy-950">{{ $contactPhone }}</span>
                                    @endif
                                </dd>
                            </div>
                        </div>
                    @endif

                    @if($contactEmail !== '')
                        <div class="flex items-start gap-4 border-t border-slate-200 py-5">
                            <svg class="mt-0.5 h-5 w-5 shrink-0 text-navy-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m3 7 9 6 9-6"></path></svg>
                            <div class="min-w-0">
                                <dt class="text-sm text-slate-500">Email</dt>
                                <dd class="mt-1 break-all"><a href="mailto:{{ $contactEmail }}" class="font-semibold text-navy-950 underline-offset-4 hover:underline">{{ $contactEmail }}</a></dd>
                            </div>
                        </div>
                    @endif

                    @if($contactAddress !== '')
                        <div class="flex items-start gap-4 border-t border-slate-200 py-5">
                            <svg class="mt-0.5 h-5 w-5 shrink-0 text-navy-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            <div>
                                <dt class="text-sm text-slate-500">Morada</dt>
                                <dd class="mt-1 whitespace-pre-line font-medium leading-relaxed text-navy-950">{{ $contactAddress }}</dd>
                            </div>
                        </div>
                    @endif

                    @if($contactHours !== '')
                        <div class="flex items-start gap-4 border-t border-slate-200 py-5">
                            <svg class="mt-0.5 h-5 w-5 shrink-0 text-navy-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v6l4 2"></path></svg>
                            <div>
                                <dt class="text-sm text-slate-500">Horário</dt>
                                <dd class="mt-1 whitespace-pre-line font-medium leading-relaxed text-navy-950">{{ $contactHours }}</dd>
                            </div>
                        </div>
                    @endif

                    @if($whatsappHref !== '')
                        <div class="flex items-start gap-4 border-t border-slate-200 py-5">
                            <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15l-1.3 4.7 4.8-1.3A10 10 0 1 0 12 2zm0 18a8 8 0 0 1-4.1-1.1l-.3-.2-2.8.8.8-2.8-.2-.3A8 8 0 1 1 12 20zm4.4-5.6c-.2-.1-1.4-.7-1.6-.8-.2-.1-.4-.1-.6.1-.2.2-.6.8-.8 1-.1.1-.3.2-.5.1a6.5 6.5 0 0 1-3.2-2.8c-.2-.4.2-.4.6-1.2.1-.1 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3a3 3 0 0 0-.9 2.2c0 1.3 1 2.6 1.1 2.7.1.2 1.9 2.9 4.6 4 .6.3 1.1.4 1.5.5.6.2 1.2.2 1.6.1.5-.1 1.4-.6 1.6-1.1.2-.6.2-1 .1-1.1z"></path></svg>
                            <div class="min-w-0">
                                <dt class="text-sm text-slate-500">WhatsApp</dt>
                                <dd class="mt-1 break-all"><a href="{{ $whatsappHref }}" target="_blank" rel="noopener" class="font-semibold text-navy-950 underline-offset-4 hover:underline">{{ $whatsappDigits }}</a></dd>
                            </div>
                        </div>
                    @endif
                </dl>
            @else
                <div class="mt-6 rounded-xl border border-dashed border-slate-300 px-6 py-10 text-center text-sm text-slate-600">
                    Os contactos ainda não estão configurados no backoffice.
                </div>
            @endif
        </div>

        {{-- Quick contact --}}
        <aside class="lg:col-span-5">
            <div class="rounded-xl bg-navy-900 p-6 text-white sm:p-8 lg:sticky lg:top-28">
                <h2 class="text-2xl font-semibold tracking-tight">Resposta rápida</h2>

                @if($whatsappHref !== '')
                    <p class="mt-2 leading-relaxed text-navy-100">
                        Escreva a sua mensagem e abrimos o WhatsApp com tudo pronto a enviar.
                    </p>

                    <form data-wa-form class="mt-6 space-y-4">
                        <div>
                            <label for="wa_nome" class="mb-1.5 block text-sm font-medium text-navy-100">O seu nome</label>
                            <input id="wa_nome" name="nome" type="text" autocomplete="name" placeholder="Nome" class="w-full rounded-lg border border-white/15 bg-white/[0.06] px-4 py-3 text-sm text-white placeholder:text-navy-300 focus:border-white/40 focus:ring-2 focus:ring-white/40">
                        </div>
                        <div>
                            <label for="wa_msg" class="mb-1.5 block text-sm font-medium text-navy-100">Mensagem</label>
                            <textarea id="wa_msg" name="mensagem" rows="3" placeholder="Em que podemos ajudar?" class="w-full rounded-lg border border-white/15 bg-white/[0.06] px-4 py-3 text-sm text-white placeholder:text-navy-300 focus:border-white/40 focus:ring-2 focus:ring-white/40"></textarea>
                        </div>
                        <x-ui.button type="submit" variant="navy-on-dark" shape="rounded" size="lg" class="w-full">
                            <svg class="h-5 w-5 text-emerald-600" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15l-1.3 4.7 4.8-1.3A10 10 0 1 0 12 2zm0 18a8 8 0 0 1-4.1-1.1l-.3-.2-2.8.8.8-2.8-.2-.3A8 8 0 1 1 12 20zm4.4-5.6c-.2-.1-1.4-.7-1.6-.8-.2-.1-.4-.1-.6.1-.2.2-.6.8-.8 1-.1.1-.3.2-.5.1a6.5 6.5 0 0 1-3.2-2.8c-.2-.4.2-.4.6-1.2.1-.1 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3a3 3 0 0 0-.9 2.2c0 1.3 1 2.6 1.1 2.7.1.2 1.9 2.9 4.6 4 .6.3 1.1.4 1.5.5.6.2 1.2.2 1.6.1.5-.1 1.4-.6 1.6-1.1.2-.6.2-1 .1-1.1z"></path></svg>
                            Enviar pelo WhatsApp
                        </x-ui.button>
                    </form>

                    <div class="my-6 border-t border-white/15"></div>

                    <x-whatsapp-button
                        :number="$contactWhatsapp"
                        message="Olá! Vi o site Maquiveloso e queria mais informações."
                        label="Falar no WhatsApp"
                        class="inline-flex min-h-11 w-full items-center justify-center rounded-lg border border-white/25 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-navy-900"
                    />
                @else
                    <p class="mt-2 leading-relaxed text-navy-100">
                        Utilize os contactos ao lado para falar connosco. Teremos todo o gosto em ajudar.
                    </p>
                    @if($phoneHref !== '' || $contactEmail !== '')
                    <div class="mt-6 space-y-3">
                        @if($phoneHref !== '')
                            <x-ui.button href="tel:{{ $phoneHref }}" variant="navy-on-dark" shape="rounded" size="md" class="w-full">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.9.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                Ligar agora
                            </x-ui.button>
                        @endif
                        @if($contactEmail !== '')
                            <x-ui.button href="mailto:{{ $contactEmail }}" variant="on-dark" shape="rounded" size="md" class="w-full">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m3 7 9 6 9-6"></path></svg>
                                Enviar email
                            </x-ui.button>
                        @endif
                    </div>
                    @endif
                @endif

                <a href="{{ route('site.catalog') }}" class="mt-6 inline-flex min-h-11 items-center text-sm font-semibold text-navy-100 underline-offset-4 hover:text-white hover:underline">
                    Ver catálogo
                </a>
            </div>
        </aside>
    </div>
</section>

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
