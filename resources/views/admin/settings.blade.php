@extends('layouts.admin')

@section('title', 'Definições')
@section('header', 'Definições')

@section('content')
<div class="max-w-3xl space-y-8">
    <div class="bg-white rounded-xl border p-6">
        <h2 class="text-lg font-semibold mb-4">Informações do Negócio</h2>

        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                    <input
                        name="business_name"
                        type="text"
                        value="{{ old('business_name', $business_name ?? '') }}"
                        class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                        placeholder="MaquiVeloso"
                        required
                    >
                    @error('business_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                    <input
                        name="contact_phone"
                        type="text"
                        value="{{ old('contact_phone', $contact_phone ?? '') }}"
                        class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                        placeholder="960 000 000"
                    >
                    @error('contact_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input
                        name="contact_email"
                        type="email"
                        value="{{ old('contact_email', $contact_email ?? '') }}"
                        class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                        placeholder="contacto@maquiveloso.pt"
                    >
                    @error('contact_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Morada</label>
                    <input
                        name="contact_address"
                        type="text"
                        value="{{ old('contact_address', $contact_address ?? '') }}"
                        class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                        placeholder="Braga, Portugal"
                    >
                    @error('contact_address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp (opcional)</label>
                    <input
                        name="contact_whatsapp"
                        type="text"
                        value="{{ old('contact_whatsapp', $contact_whatsapp ?? '') }}"
                        class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                        placeholder="+351960000000"
                    >
                    @error('contact_whatsapp')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Horário (opcional)</label>
                    <input
                        name="contact_hours"
                        type="text"
                        value="{{ old('contact_hours', $contact_hours ?? '') }}"
                        class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                        placeholder="Seg-Sex: 09:00-18:00"
                    >
                    @error('contact_hours')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <button class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800">
                    Guardar alterações
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
