@extends('layouts.admin')

@section('title', 'Máquinas')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;

    $q        = request('q', '');
    $category = request('category', '');
    $status   = request('status', '');

    $statusLabels = [
        'available' => 'Disponível',
        'reserved'  => 'Reservada',
        'sold'      => 'Vendida',
        'inactive'  => 'Indisponível',
    ];

    $badgeClass = function (?string $s) {
        return match ($s) {
            'available' => 'bg-green-100 text-green-700',
            'reserved'  => 'bg-yellow-100 text-yellow-700',
            'sold'      => 'bg-blue-100 text-blue-700',
            'inactive'  => 'bg-gray-200 text-gray-700',
            default     => 'bg-gray-200 text-gray-700',
        };
    };
@endphp

<div class="flex items-center justify-between">
    <div>
        <h1 class="text-4xl font-extrabold tracking-tight text-gray-900">Máquinas</h1>
    </div>

    <a href="{{ route('admin.machines.create') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-gray-800">
        <span class="text-lg leading-none">+</span>
        Nova Máquina
    </a>
</div>

<div class="mt-8 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
    <form method="GET" action="{{ route('admin.machines.index') }}">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
            <div class="lg:col-span-8">
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-gray-400">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="M21 21l-4.3-4.3"></path>
                        </svg>
                    </span>

                    <input
                        name="q"
                        value="{{ $q }}"
                        placeholder="Pesquisar por nome..."
                        class="w-full rounded-xl border-gray-200 bg-white py-3 pl-12 pr-4 text-sm shadow-sm focus:border-gray-900 focus:ring-gray-900"
                    />
                </div>
            </div>

            <div class="lg:col-span-2">
                <select
                    name="category"
                    class="w-full rounded-xl border-gray-200 bg-white py-3 text-sm shadow-sm focus:border-gray-900 focus:ring-gray-900"
                >
                    <option value="">Categorias</option>
                    @foreach(($categories ?? []) as $cat)
                        @php
                            $catId   = is_object($cat) ? ($cat->id ?? null) : null;
                            $catName = is_object($cat) ? ($cat->name ?? $cat->nome ?? '') : (string)$cat;
                        @endphp
                        @if($catId !== null)
                            <option value="{{ $catId }}" @selected((string)$category === (string)$catId)>{{ $catName }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-2">
                <select
                    name="status"
                    class="w-full rounded-xl border-gray-200 bg-white py-3 text-sm shadow-sm focus:border-gray-900 focus:ring-gray-900"
                >
                    <option value="">Estado</option>
                    @foreach($statusLabels as $key => $label)
                        <option value="{{ $key }}" @selected($status === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-4 flex items-center justify-between">
            <div class="text-sm text-gray-500">
                @if(isset($machines))
                    {{ method_exists($machines, 'total') ? $machines->total() : $machines->count() }} resultado(s)
                @endif
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.machines.index') }}"
                   class="text-sm font-semibold text-gray-600 hover:text-gray-900">
                    Limpar
                </a>

                <button
                    class="rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-gray-800">
                    Filtrar
                </button>
            </div>
        </div>
    </form>
</div>

<div class="mt-8 rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b border-gray-100">
                    <th class="px-6 py-4 font-semibold">Foto</th>
                    <th class="px-6 py-4 font-semibold">Nome</th>
                    <th class="px-6 py-4 font-semibold">Categoria</th>
                    <th class="px-6 py-4 font-semibold">Preço</th>
                    <th class="px-6 py-4 font-semibold">Estado</th>
                    <th class="px-6 py-4 font-semibold">Neg.</th>
                    <th class="px-6 py-4 font-semibold">Data</th>
                    <th class="px-6 py-4 font-semibold text-right">Ações</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse(($machines ?? []) as $machine)
                    @php
                        $img = null;
                        if (isset($machine->images) && $machine->images?->count()) $img = $machine->images->first();
                        if (!$img && isset($machine->machineImages) && $machine->machineImages?->count()) $img = $machine->machineImages->first();

                        $imgUrl = null;
                        if ($img) {
                            $path = $img->path ?? $img->url ?? $img->image_path ?? null;
                            if ($path) {
                                if (str_starts_with($path, 'http')) {
                                    $imgUrl = $path;
                                } else {
                                    $path = ltrim($path, '/');
                                    if (str_starts_with($path, 'public/')) $path = substr($path, 7);
                                    $imgUrl = Storage::url($path);
                                }
                            }
                        }

                        $mName       = $machine->name ?? $machine->nome ?? '—';
                        $mCategory   = $machine->category->name ?? $machine->category->nome ?? '—';
                        $mPrice      = $machine->price ?? $machine->preco ?? null;
                        $mStatus     = $machine->status ?? $machine->estado ?? null;
                        $mNegotiable = (bool)($machine->negotiable ?? false);
                        $createdAt   = $machine->created_at ?? null;

                        $updateStatusUrl = route('admin.machines.updateStatus', $machine);
                    @endphp

                    <tr class="hover:bg-gray-50/60">
                        <td class="px-6 py-4">
                            <div class="h-14 w-14 rounded-xl bg-gray-100 overflow-hidden ring-1 ring-gray-200">
                                @if($imgUrl)
                                    <img src="{{ $imgUrl }}" alt="" class="h-full w-full object-cover">
                                @else
                                    <div class="h-full w-full flex items-center justify-center text-gray-400">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                                            <path d="M3 16l5-5 4 4 3-3 6 6"></path>
                                            <path d="M14 8h.01"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-900">{{ $mName }}</div>
                        </td>

                        <td class="px-6 py-4 text-gray-800">
                            {{ $mCategory }}
                        </td>

                        <td class="px-6 py-4 text-gray-800">
                            @if($mPrice === null || $mPrice === '')
                                -
                            @else
                                {{ number_format((float)$mPrice, 0, ',', '.') }} €
                            @endif
                        </td>

                        <td class="px-6 py-4">
                            <span
                                class="inline-flex items-center rounded-lg px-3 py-1 text-xs font-semibold {{ $badgeClass($mStatus) }}"
                                data-status-badge
                            >
                                {{ $statusLabels[$mStatus] ?? ucfirst((string)$mStatus) }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            @if($mNegotiable)
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-gray-900 text-white text-xs font-bold" title="Negociável">N</span>
                            @else
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-gray-100 text-gray-500 text-xs font-bold" title="Não negociável">—</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-gray-500">
                            @if($createdAt)
                                {{ \Carbon\Carbon::parse($createdAt)->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.machines.edit', $machine) }}"
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-800 hover:bg-gray-50">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 20h9"></path>
                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                                    </svg>
                                </a>

                                <div class="relative inline-flex">
                                    <button
                                        type="button"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50"
                                        title="Alterar estado"
                                    >
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 21v-7"></path>
                                            <path d="M4 10V3"></path>
                                            <path d="M12 21v-9"></path>
                                            <path d="M12 8V3"></path>
                                            <path d="M20 21v-5"></path>
                                            <path d="M20 12V3"></path>
                                            <path d="M2 14h4"></path>
                                            <path d="M10 8h4"></path>
                                            <path d="M18 16h4"></path>
                                        </svg>
                                    </button>

                                    <select
                                        class="absolute inset-0 h-9 w-9 opacity-0 cursor-pointer"
                                        aria-label="Alterar estado"
                                        data-status-select
                                        data-update-url="{{ $updateStatusUrl }}"
                                    >
                                        @foreach($statusLabels as $key => $label)
                                            <option value="{{ $key }}" @selected($mStatus === $key)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center text-gray-500">
                            Ainda não tens máquinas. Clica em <span class="font-semibold">Nova Máquina</span>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(isset($machines) && method_exists($machines, 'links'))
        <div class="border-t border-gray-100 px-6 py-4">
            {{ $machines->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<script>
(function () {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const labelMap = {
        available: 'Disponível',
        reserved: 'Reservada',
        sold: 'Vendida',
        inactive: 'Indisponível',
    };

    const classMap = {
        available: 'bg-green-100 text-green-700',
        reserved: 'bg-yellow-100 text-yellow-700',
        sold: 'bg-blue-100 text-blue-700',
        inactive: 'bg-gray-200 text-gray-700',
    };

    document.querySelectorAll('[data-status-select]').forEach((select) => {
        let last = select.value;

        select.addEventListener('change', async () => {
            const url = select.dataset.updateUrl;
            const value = select.value;

            select.disabled = true;

            try {
                const res = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ status: value }),
                });

                if (!res.ok) {
                    select.value = last;
                    alert('Não foi possível atualizar o estado.');
                    return;
                }

                last = value;

                const row = select.closest('tr');
                const badge = row?.querySelector('[data-status-badge]');

                if (badge) {
                    badge.textContent = labelMap[value] ?? value;

                    badge.classList.remove(
                        'bg-green-100','text-green-700',
                        'bg-yellow-100','text-yellow-700',
                        'bg-blue-100','text-blue-700',
                        'bg-gray-200','text-gray-700'
                    );

                    const cls = (classMap[value] || 'bg-gray-200 text-gray-700').split(' ');
                    cls.forEach(c => badge.classList.add(c));
                }
            } catch (e) {
                select.value = last;
                alert('Erro de rede ao atualizar o estado.');
            } finally {
                select.disabled = false;
            }
        });
    });
})();
</script>
@endsection
