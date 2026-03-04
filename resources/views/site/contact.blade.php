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

    $whatsappHref = '';
    if ($contactWhatsapp !== '') {
        if (str_starts_with($contactWhatsapp, 'http://') || str_starts_with($contactWhatsapp, 'https://')) {
            $whatsappHref = $contactWhatsapp;
        } else {
            $digits = preg_replace('/\D+/', '', $contactWhatsapp);
            if (is_string($digits) && $digits !== '') {
                $whatsappHref = 'https://wa.me/' . $digits;
            }
        }
    }

    $hasContactLines = $contactPhone !== '' || $contactEmail !== '' || $contactAddress !== '' || $whatsappHref !== '' || $contactHours !== '';
    $whatsappButtonUrl = 'https://wa.me/351960125103?text=Ol%C3%A1%21%20Vi%20o%20site%20Maquiveloso%20e%20queria%20mais%20informa%C3%A7%C3%B5es.';
@endphp

<section class="max-w-3xl mx-auto px-6 py-16">
    <h1 class="text-3xl font-bold mb-8">Contacto</h1>

    <div class="bg-white border rounded-xl p-6 space-y-4">
        <p class="text-sm text-gray-700">
            O formulário de contacto online está temporariamente indisponível.
        </p>
        <p class="text-sm text-gray-700">
            Para pedidos, por favor utilize os contactos no rodapé do site.
        </p>

        @if($hasContactLines)
            <div class="border-t border-gray-100 pt-4 space-y-2">
                @if($contactPhone !== '' && $phoneHref !== '')
                    <p class="text-sm text-gray-700">
                        Telefone:
                        <a href="tel:{{ $phoneHref }}" class="font-medium text-gray-900 hover:underline">
                            {{ $contactPhone }}
                        </a>
                    </p>
                @endif

                @if($contactEmail !== '')
                    <p class="text-sm text-gray-700">
                        Email:
                        <a href="mailto:{{ $contactEmail }}" class="font-medium text-gray-900 hover:underline">
                            {{ $contactEmail }}
                        </a>
                    </p>
                @endif

                @if($contactAddress !== '')
                    <p class="text-sm text-gray-700 whitespace-pre-line">
                        Morada: {{ $contactAddress }}
                    </p>
                @endif

                @if($whatsappHref !== '')
                    <p class="text-sm text-gray-700">
                        WhatsApp:
                        <a href="{{ $whatsappHref }}" target="_blank" rel="noopener" class="font-medium text-gray-900 hover:underline">
                            {{ $contactWhatsapp }}
                        </a>
                    </p>
                @endif

                @if($contactHours !== '')
                    <p class="text-sm text-gray-700 whitespace-pre-line">
                        Horário: {{ $contactHours }}
                    </p>
                @endif
            </div>
        @endif

        <a
            href="{{ $whatsappButtonUrl }}"
            target="_blank"
            rel="noopener"
            class="inline-flex px-6 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700"
        >
            Falar no WhatsApp
        </a>

        <a href="{{ route('site.home') }}" class="inline-flex px-6 py-3 bg-gray-900 text-white rounded-lg font-medium hover:bg-gray-800">
            Voltar ao início
        </a>
    </div>
</section>
@endsection
