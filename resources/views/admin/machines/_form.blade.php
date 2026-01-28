@php
    $isEdit = ($mode ?? 'create') === 'edit';

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
        'available' => 'Disponível',
        'reserved'  => 'Reservada',
        'sold'      => 'Vendida',
        'inactive'  => 'Indisponível',
    ];

    $selectedCategory = (string) $val('category_id', '');
    $selectedStatus   = (string) $val('status', 'available');

    $featuredVal = $val('featured', 0);
    $isFeatured  = (string)$featuredVal === '1' || $featuredVal === 1 || $featuredVal === true;

    $existingImages = collect();

    if (isset($machine) && $machine) {
        if (isset($machine->images) && $machine->images) $existingImages = $existingImages->merge($machine->images);
        if (isset($machine->machineImages) && $machine->machineImages) $existingImages = $existingImages->merge($machine->machineImages);
    }

    $existingImages = $existingImages
        ->filter()
        ->unique(fn ($img) => $img->id ?? spl_object_id($img))
        ->values();

    $imgUrlFrom = function ($img) {
        $path = $img->path ?? $img->url ?? $img->image_path ?? null;
        if (!$path) return null;
        return str_starts_with($path, 'http') ? $path : asset('storage/' . ltrim($path, '/'));
    };
@endphp

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

{{-- Imagens existentes (FORA do form principal para não haver forms aninhados) --}}
@if($isEdit && $existingImages->count())
    <div>
        <div class="text-sm font-semibold text-gray-900">Imagens atuais</div>

        <div class="mt-3 grid grid-cols-4 sm:grid-cols-6 gap-3">
            @foreach($existingImages as $img)
                @php $u = $imgUrlFrom($img); @endphp

                <div class="relative h-20 w-20 overflow-hidden rounded-xl ring-1 ring-gray-200 bg-gray-100">
                    @if($u)
                        <img src="{{ $u }}" alt="" class="h-full w-full object-cover">
                    @endif

                    @if(isset($img->id))
                        <form
                            method="POST"
                            action="{{ route('admin.machines.images.destroy', [$machine, $img]) }}"
                            onsubmit="return confirm('Remover esta imagem?');"
                            class="absolute top-1 right-1"
                        >
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="h-7 w-7 rounded-lg bg-white/90 border border-gray-200 text-gray-700 hover:bg-white shadow-sm flex items-center justify-center"
                                title="Remover"
                            >
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M18 6L6 18"></path>
                                    <path d="M6 6l12 12"></path>
                                </svg>
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif

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

    {{-- Upload novas imagens --}}
    <div>
        <div class="text-sm font-semibold text-gray-900">Adicionar imagens</div>

        <div id="new-images-preview" class="mt-3 grid grid-cols-4 sm:grid-cols-6 gap-3"></div>

        <div class="mt-3 flex items-start gap-4">
            <label class="group relative flex h-20 w-20 cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-white text-center hover:border-gray-900">
                <input id="imagesInput" type="file" name="images[]" class="absolute inset-0 opacity-0 cursor-pointer" multiple accept="image/*">
                <svg class="h-5 w-5 text-gray-500 group-hover:text-gray-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 3v12"></path>
                    <path d="M7 8l5-5 5 5"></path>
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                </svg>
                <div class="mt-1 text-xs text-gray-500 group-hover:text-gray-900">Adicionar</div>
            </label>

            <div class="text-xs text-gray-500 leading-relaxed pt-1">
                Podes selecionar várias imagens (Ctrl/Shift no seletor).
                <br>
                Máx: 6MB por imagem.
            </div>
        </div>
    </div>

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

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">
                Categoria
            </label>
            <select
                name="category_id"
                class="w-full rounded-xl border-gray-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-gray-900 focus:ring-gray-900"
            >
                <option value="">— Sem categoria —</option>

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

<script>
(function () {
    const input = document.getElementById('imagesInput');
    const preview = document.getElementById('new-images-preview');
    if (!input || !preview) return;

    input.addEventListener('change', function () {
        preview.innerHTML = '';
        const files = Array.from(input.files || []);
        files.forEach((file) => {
            if (!file.type || !file.type.startsWith('image/')) return;

            const url = URL.createObjectURL(file);

            const wrap = document.createElement('div');
            wrap.className = 'h-20 w-20 overflow-hidden rounded-xl ring-1 ring-gray-200 bg-gray-100';

            const img = document.createElement('img');
            img.src = url;
            img.className = 'h-full w-full object-cover';
            img.onload = () => URL.revokeObjectURL(url);

            wrap.appendChild(img);
            preview.appendChild(wrap);
        });
    });
})();
</script>
