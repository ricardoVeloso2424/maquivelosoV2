<div>
    <label class="block text-sm font-medium text-gray-700 mb-2">Nome *</label>
    <input
        name="name"
        value="{{ old('name', $category->name) }}"
        class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-gray-400 focus:ring-gray-400"
        placeholder="Ex: Doméstica"
        required
    />
    @error('name')
        <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
    @enderror
</div>
