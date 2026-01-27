@php
$isEdit = isset($machine) && $machine->exists;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $machine->name ?? '') }}"
            class="w-full rounded-md border-gray-300 focus:border-gray-900 focus:ring-gray-900"
            required>
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
        <input
            type="text"
            name="brand"
            value="{{ old('brand', $machine->brand ?? '') }}"
            class="w-full rounded-md border-gray-300 focus:border-gray-900 focus:ring-gray-900">
        @error('brand') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Modelo</label>
        <input
            type="text"
            name="model"
            value="{{ old('model', $machine->model ?? '') }}"
            class="w-full rounded-md border-gray-300 focus:border-gray-900 focus:ring-gray-900">
        @error('model') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Preço (€)</label>
        <input
            type="number"
            step="0.01"
            min="0"
            name="price"
            value="{{ old('price', $machine->price ?? '') }}"
            class="w-full rounded-md border-gray-300 focus:border-gray-900 focus:ring-gray-900">
        @error('price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
        <select
            name="status"
            class="w-full rounded-md border-gray-300 focus:border-gray-900 focus:ring-gray-900"
            required>
            @php
            $status = old('status', $machine->status ?? 'active');
            @endphp
            <option value="active" @selected($status==='active' )>Ativa</option>
            <option value="inactive" @selected($status==='inactive' )>Inativa</option>
        </select>
        @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
        <textarea
            name="description"
            rows="4"
            class="w-full rounded-md border-gray-300 focus:border-gray-900 focus:ring-gray-900">{{ old('description', $machine->description ?? '') }}</textarea>
        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>