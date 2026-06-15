@extends('layouts.admin')

@section('title', 'Definições')

@section('content')
@php
    $inputClass = 'w-full rounded-xl border-stone-300 text-sm transition focus:border-brand-500 focus:ring-brand-500';
@endphp
<div class="max-w-3xl space-y-8">
    <div>
        <span class="inline-flex items-center gap-2.5 text-xs font-semibold uppercase tracking-[0.2em] text-brand-700">
            <span class="h-px w-7 stitch-line"></span>Configuração
        </span>
        <h1 class="mt-2 font-serif text-4xl font-semibold tracking-tight text-stone-900">Definições</h1>
        <p class="mt-2 text-sm text-stone-500">Estes dados alimentam o cabeçalho, o rodapé e a página de contacto do site.</p>
    </div>

    <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-card sm:p-8">
        <h2 class="font-serif text-xl font-semibold text-stone-900">Informações do Negócio</h2>

        <form method="POST" action="{{ route('admin.settings.update') }}" class="mt-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-stone-700">Nome</label>
                    <input name="business_name" type="text" value="{{ old('business_name', $business_name ?? '') }}" class="{{ $inputClass }}" placeholder="MaquiVeloso" required>
                    @error('business_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-stone-700">Telefone</label>
                    <input name="contact_phone" type="text" value="{{ old('contact_phone', $contact_phone ?? '') }}" class="{{ $inputClass }}" placeholder="960 000 000">
                    @error('contact_phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-stone-700">Email</label>
                    <input name="contact_email" type="email" value="{{ old('contact_email', $contact_email ?? '') }}" class="{{ $inputClass }}" placeholder="contacto@maquiveloso.pt">
                    @error('contact_email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-stone-700">Morada</label>
                    <input name="contact_address" type="text" value="{{ old('contact_address', $contact_address ?? '') }}" class="{{ $inputClass }}" placeholder="Braga, Portugal">
                    @error('contact_address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-stone-700">WhatsApp (opcional)</label>
                    <input name="contact_whatsapp" type="text" value="{{ old('contact_whatsapp', $contact_whatsapp ?? '') }}" class="{{ $inputClass }}" placeholder="+351960000000">
                    @error('contact_whatsapp')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-stone-700">Horário (opcional)</label>
                    <input name="contact_hours" type="text" value="{{ old('contact_hours', $contact_hours ?? '') }}" class="{{ $inputClass }}" placeholder="Seg-Sex: 09:00-18:00">
                    @error('contact_hours')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="border-t border-stone-100 pt-6">
                <x-ui.button type="submit" variant="primary" size="md">Guardar alterações</x-ui.button>
            </div>
        </form>
    </div>
</div>
@endsection
