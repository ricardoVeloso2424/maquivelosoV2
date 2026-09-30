@php
    $isEdit = ($mode ?? 'create') === 'edit';

    $val = function ($key, $fallback = '') use ($machine) {
        if (!$machine) return old($key, $fallback);

        return old($key, $machine->{$key} ?? $fallback);
    };

    $statusOptions = config('machines.statuses');

    $selectedCategory = (string) $val('category_id', '');
    $selectedStatus   = (string) $val('status', 'available');

    $featuredVal = $val('featured', 0);
    $isFeatured  = (string)$featuredVal === '1' || $featuredVal === 1 || $featuredVal === true;

    $negVal = $val('negotiable', 0);
    $isNegotiable = (string)$negVal === '1' || $negVal === 1 || $negVal === true;

    $existingImages = collect();

    if (isset($machine) && $machine) {
        if (isset($machine->images) && $machine->images) $existingImages = $existingImages->merge($machine->images);
    }

    $existingImages = $existingImages
        ->filter()
        ->unique(fn ($img) => $img->id ?? spl_object_id($img))
        ->values();

    $inputClass = 'w-full rounded-xl border-stone-300 bg-white px-4 py-3 text-sm shadow-sm transition focus:border-brand-500 focus:ring-brand-500';
@endphp

@if ($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <div class="mb-2 font-semibold">Corrige estes erros:</div>
        <ul class="list-disc space-y-1 pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if($isEdit && $existingImages->count())
    <div class="mt-6">
        <div class="text-sm font-semibold text-stone-900">Imagens atuais</div>
        <p class="mt-1 text-xs text-stone-500">
            A imagem principal é a que aparece no catálogo, na página inicial e no topo do detalhe.
            Se não escolheres nenhuma, é usada a primeira.
        </p>

        <div class="mt-3 grid grid-cols-4 gap-3 sm:grid-cols-6">
            @foreach($existingImages as $img)
                @php
                    $u = $img->thumb_url;
                    $isMain = (bool) ($img->is_featured ?? false);
                @endphp

                <div class="flex flex-col items-center gap-1">
                    <div class="relative h-20 w-20 overflow-hidden rounded-xl bg-stone-100 ring-1 {{ $isMain ? 'ring-2 ring-brand-600' : 'ring-stone-200' }}">
                        @if($u)
                            <img src="{{ $u }}" alt="" width="80" height="80" loading="lazy" decoding="async" class="h-full w-full object-cover">
                        @endif

                        @if($isMain)
                            <span class="absolute left-1 top-1 rounded bg-brand-600 px-1.5 py-0.5 text-[10px] font-semibold text-white">Principal</span>
                        @endif

                        @if(isset($img->id) && isset($machine->id))
                            <form
                                method="POST"
                                action="{{ route('admin.machines.images.destroy', ['machine' => $machine->id, 'image' => $img->id]) }}"
                                onsubmit="return confirm('Remover esta imagem?');"
                                class="absolute right-1 top-1"
                            >
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="flex h-7 w-7 items-center justify-center rounded-lg border border-stone-200 bg-white/90 text-stone-600 shadow-sm transition hover:bg-white hover:text-red-600"
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

                    @if(isset($img->id) && isset($machine->id) && !$isMain)
                        <form
                            method="POST"
                            action="{{ route('admin.machines.images.feature', ['machine' => $machine->id, 'image' => $img->id]) }}"
                        >
                            @csrf
                            @method('PATCH')
                            <button
                                type="submit"
                                class="text-[11px] font-medium text-stone-500 underline-offset-2 transition hover:text-brand-700 hover:underline"
                            >
                                Tornar principal
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif

<form
    id="machineForm"
    method="POST"
    action="{{ $isEdit ? route('admin.machines.update', $machine) : route('admin.machines.store') }}"
    enctype="multipart/form-data"
    class="mt-6 space-y-8"
>
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div>
        <div class="text-sm font-semibold text-stone-900">Adicionar imagens</div>

        <div id="new-images-preview" class="mt-3 grid grid-cols-4 gap-3 sm:grid-cols-6"></div>

        <div class="mt-3 flex items-start gap-4">
            <label class="group relative flex h-20 w-20 cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-stone-300 bg-white text-center transition hover:border-brand-500">
                <input id="imagesInput" type="file" name="images[]" class="absolute inset-0 cursor-pointer opacity-0" multiple accept="image/*">
                <svg class="h-5 w-5 text-stone-400 transition group-hover:text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 3v12"></path>
                    <path d="M7 8l5-5 5 5"></path>
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                </svg>
                <div class="mt-1 text-xs text-stone-500 transition group-hover:text-brand-600">Adicionar</div>
            </label>

            <div class="pt-1 text-xs leading-relaxed text-stone-500">
                Podes selecionar várias imagens (Ctrl).
                <br>
                Máx: 8 imagens, 5MB por imagem.
            </div>
        </div>
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-stone-900">
            Nome <span class="text-red-500">*</span>
        </label>
        <input type="text" name="name" value="{{ $val('name') }}" placeholder="Ex: Singer Tradition 2250" class="{{ $inputClass }}" required>
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-stone-900">Descrição</label>
        <textarea name="description" rows="5" placeholder="Descreva a máquina..." class="{{ $inputClass }}">{{ $val('description') }}</textarea>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div>
            <label class="mb-2 block text-sm font-semibold text-stone-900">Categoria</label>
            <select name="category_id" class="{{ $inputClass }}">
                <option value="">— Sem categoria —</option>
                @foreach(($categories ?? []) as $cat)
                    @php
                        $catId = is_object($cat) ? ($cat->id ?? null) : null;
                        $catName = is_object($cat) ? ($cat->name ?? '') : (string)$cat;
                    @endphp
                    @if($catId !== null)
                        <option value="{{ $catId }}" @selected($selectedCategory === (string)$catId)>{{ $catName }}</option>
                    @endif
                @endforeach
            </select>
        </div>

        <div>
            <label class="mb-2 block text-sm font-semibold text-stone-900">Preço (€)</label>
            <input type="text" inputmode="decimal" name="price" value="{{ $val('price') }}" placeholder="Deixe vazio para 'sob consulta'" class="{{ $inputClass }}">
        </div>
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-stone-900">
            Estado <span class="text-red-500">*</span>
        </label>
        <select name="status" class="{{ $inputClass }}" required>
            @foreach($statusOptions as $key => $label)
                <option value="{{ $key }}" @selected($selectedStatus === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-stone-200 bg-white px-4 py-3 transition hover:border-stone-300">
            <input id="featured" type="checkbox" name="featured" value="1" @checked($isFeatured) class="h-4 w-4 rounded border-stone-300 text-brand-600 focus:ring-brand-500">
            <span class="text-sm font-semibold text-stone-900">Destacar na página inicial</span>
        </label>

        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-stone-200 bg-white px-4 py-3 transition hover:border-stone-300">
            <input id="negotiable" type="checkbox" name="negotiable" value="1" @checked($isNegotiable) class="h-4 w-4 rounded border-stone-300 text-brand-600 focus:ring-brand-500">
            <span class="text-sm font-semibold text-stone-900">Negociável</span>
        </label>
    </div>

    <div class="grid grid-cols-1 gap-4 pt-4 md:grid-cols-2">
        <x-ui.button :href="route('admin.machines.index')" variant="outline" size="lg" class="w-full">Cancelar</x-ui.button>

        <x-ui.button id="submitBtn" type="submit" variant="primary" size="lg" class="w-full">
            {{ $isEdit ? 'Guardar Alterações' : 'Criar Máquina' }}
        </x-ui.button>
    </div>
</form>

<script>
(function () {
    const input = document.getElementById('imagesInput');
    const preview = document.getElementById('new-images-preview');
    if (input && preview) {
        input.addEventListener('change', function () {
            preview.innerHTML = '';
            const files = Array.from(input.files || []);
            files.forEach((file) => {
                if (!file.type || !file.type.startsWith('image/')) return;

                const url = URL.createObjectURL(file);

                const wrap = document.createElement('div');
                wrap.className = 'h-20 w-20 overflow-hidden rounded-xl ring-1 ring-stone-200 bg-stone-100';

                const img = document.createElement('img');
                img.src = url;
                img.className = 'h-full w-full object-cover';
                img.onload = () => URL.revokeObjectURL(url);

                wrap.appendChild(img);
                preview.appendChild(wrap);
            });
        });
    }

    const form = document.getElementById('machineForm');
    const btn = document.getElementById('submitBtn');
    if (form && btn) {
        form.addEventListener('submit', function () {
            btn.disabled = true;
            btn.classList.add('opacity-70', 'cursor-not-allowed');
        });
    }
})();
</script>
