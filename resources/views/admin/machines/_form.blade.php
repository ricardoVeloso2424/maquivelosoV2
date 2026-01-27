@php
    $isEdit = ($mode ?? 'create') === 'edit';

    // tenta apanhar campos com nomes diferentes (caso o teu model use pt/en)
    $val = function ($key, $fallback = '') use ($machine) {
        if (!$machine) return old($key, $fallback);

        $map = [
            'name' => ['name', 'nome'],
            'description' => ['description', 'descricao'],
            'category_id' => ['category_id', 'categoria_id'],
            'price' => ['price', 'preco'],
            'status' => ['status', 'estado'],
            'featured' => ['featured', 'destaque', 'is_featured'],
        ];

        $candidates = $map[$key] ?? [$key];
        foreach ($candidates as $cand) {
            if (isset($machine->{$cand}) && $machine->{$cand} !== null) {
                return old($key, $machine->{$cand});
            }
        }

        return old($key, $fallback);
    };

    $statusOptions = [
        'disponivel'   => 'Disponível',
        'reservada'    => 'Reservada',
        'vendida'      => 'Vendida',
        'indisponivel' => 'Indisponível',
    ];

    $selectedCategory = (string) $val('category_id', '');
    $selectedStatus = (string) $val('status', 'disponivel');

    $featuredVal = $val('featured', 0);
    $isFeatured = (string)$featuredVal === '1' || $featuredVal === 1 || $featuredVal === true;
@endphp

<form
    method="POST"
    action="{{ $isEdit ? route('admin.machines.update', $machine) : route('admin.machines.store') }}"
    enctype="multipart/form-data"
    class="space-y-8"
>
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    {{-- Erros --}}
    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <div class="font-semibold mb-2">Corrige estes erros:</div>
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Imagens --}}
    <div>
        <div class="text-sm font-semibold text-gray-900">Imagens</div>
        <div class="mt-3 flex items-start gap-4">
            {{-- Preview (1ª imagem se existir) --}}
            @php
                $previewUrl = null;

                if (isset($machine) && $machine) {
                    $img = null;
                    if (isset($machine->images) && $machine->images?->count()) $img = $machine->images->first();
                    if (!$img && isset($machine->machineImages) && $machine->machineImages?->count()) $img = $machine->machineImages->first();

                    if ($img) {
                        $path = $img->path ?? $img->url ?? $img->image_path ?? null;
                        if ($path) $previewUrl = str_starts_with($path, 'http') ? $path : asset('storage/'.$path);
                    }
                }
            @endphp

            @if($previewUrl)
                <div class="h-20 w-20 overflow-hidden rounded-xl ring-1 ring-gray-200 bg-gray-100">
                    <img src="{{ $previewUrl }}" alt="" class="h-full w-full object-cover">
                </div>
            @endif

            {{-- Caixa upload --}}
            <label class="group relative flex h-20 w-20 cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-white text-center hover:border-gray-900">
                <input type="file" name="images[]" class="absolute inset-0 opacity-0 cursor-pointer" multiple accept="image/*">
                <svg class="h-5 w-5 text-gray-500 group-hover:text-gray-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 3v12"></path>
                    <path d="M7 8l5-5 5 5"></path>
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                </svg>
                <div class="mt-1 text-xs text-gray-500 group-hover:text-gray-900">Adicionar</div>
            </label>
        </div>
    </div>

    {{-- Nome --}}
    <div>
        <label class="block text-sm font-semibold text-gray-900 mb-2">
            Nome <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            name="name"
            value="{{ $val('name') }}"
            placeholder="Ex: Singer Tradition 2250"
            class="w-full rounded-xl border-gray-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-gray-900 focus:ring-gray-900"
            required
        />
    </div>

    {{-- Descrição --}}
    <div>
        <label class="block text-sm font-semibold text-gray-900 mb-2">
            Descrição
        </label>
        <textarea
            name="description"
            rows="5"
            placeholder="Descreva a máquina..."
            class="w-full rounded-xl border-gray-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-gray-900 focus:ring-gray-900"
        >{{ $val('description') }}</textarea>
    </div>

    {{-- Categoria + Preço --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">
                Categoria <span class="text-red-500">*</span>
            </label>
            <select
                name="category_id"
                class="w-full rounded-xl border-gray-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-gray-900 focus:ring-gray-900"
                required
            >
                @foreach(($categories ?? []) as $cat)
                    @php
                        $catId = is_object($cat) ? ($cat->id ?? null) : null;
                        $catName = is_object($cat) ? ($cat->name ?? $cat->nome ?? '') : (string)$cat;
                    @endphp
                    @if($catId !== null)
                        <option value="{{ $catId }}" @selected($selectedCategory === (string)$catId)>{{ $catName }}</option>
                    @endif
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">
                Preço (€)
            </label>
            <input
                type="text"
                inputmode="decimal"
                name="price"
                value="{{ $val('price') }}"
                placeholder="Deixe vazio para 'sob consulta'"
                class="w-full rounded-xl border-gray-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-gray-900 focus:ring-gray-900"
            />
        </div>
    </div>

    {{-- Estado --}}
    <div>
        <label class="block text-sm font-semibold text-gray-900 mb-2">
            Estado <span class="text-red-500">*</span>
        </label>
        <select
            name="status"
            class="w-full rounded-xl border-gray-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-gray-900 focus:ring-gray-900"
            required
        >
            @foreach($statusOptions as $key => $label)
                <option value="{{ $key }}" @selected($selectedStatus === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    {{-- Destaque --}}
    <div class="flex items-center gap-3">
        <input
            id="featured"
            type="checkbox"
            name="featured"
            value="1"
            @checked($isFeatured)
            class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900"
        />
        <label for="featured" class="text-sm font-semibold text-gray-900">
            Destacar na página inicial
        </label>
    </div>

    {{-- Ações --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4">
        <a href="{{ route('admin.machines.index') }}"
           class="inline-flex w-full items-center justify-center rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm font-semibold text-gray-800 hover:bg-gray-50">
            Cancelar
        </a>

        <button
            type="submit"
            class="inline-flex w-full items-center justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-gray-800"
        >
            {{ $isEdit ? 'Guardar Alterações' : 'Criar Máquina' }}
        </button>
    </div>
</form>
