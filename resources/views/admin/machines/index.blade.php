@extends('layouts.admin')

@section('title', 'Máquinas')

@section('content')
@php
    $q        = request('q', '');
    $category = request('category', '');
    $status   = request('status', '');

    $statusLabels = config('machines.statuses');

    $badgeClass = function (?string $s) {
        return match ($s) {
            'available' => 'bg-emerald-100 text-emerald-700',
            'reserved'  => 'bg-amber-100 text-amber-700',
            'sold'      => 'bg-sky-100 text-sky-700',
            'inactive'  => 'bg-stone-200 text-stone-600',
            default     => 'bg-stone-200 text-stone-600',
        };
    };
@endphp

<div class="flex items-center justify-between gap-4">
    <div>
        <span class="inline-flex items-center gap-2.5 text-xs font-semibold uppercase tracking-[0.2em] text-brand-700">
            <span class="h-px w-7 stitch-line"></span>Catálogo
        </span>
        <h1 class="mt-2 font-serif text-4xl font-semibold tracking-tight text-stone-900">Máquinas</h1>
    </div>

    <x-ui.button :href="route('admin.machines.create')" variant="primary" size="md">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg>
        Nova Máquina
    </x-ui.button>
</div>

<div class="mt-8 rounded-2xl border border-stone-200 bg-white p-6 shadow-card">
    <form method="GET" action="{{ route('admin.machines.index') }}">
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">
            <div class="lg:col-span-8">
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-stone-400">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="M21 21l-4.3-4.3"></path>
                        </svg>
                    </span>

                    <input
                        name="q"
                        value="{{ $q }}"
                        placeholder="Pesquisar por nome, marca ou modelo..."
                        class="w-full rounded-xl border-stone-300 bg-white py-3 pl-12 pr-4 text-sm transition focus:border-brand-500 focus:ring-brand-500"
                    />
                </div>
            </div>

            <div class="lg:col-span-2">
                <select
                    name="category"
                    class="w-full rounded-xl border-stone-300 bg-white py-3 text-sm transition focus:border-brand-500 focus:ring-brand-500"
                >
                    <option value="">Categorias</option>
                    @foreach(($categories ?? []) as $cat)
                        @php
                            $catId   = is_object($cat) ? ($cat->id ?? null) : null;
                            $catName = is_object($cat) ? ($cat->name ?? '') : (string)$cat;
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
                    class="w-full rounded-xl border-stone-300 bg-white py-3 text-sm transition focus:border-brand-500 focus:ring-brand-500"
                >
                    <option value="">Estado</option>
                    @foreach($statusLabels as $key => $label)
                        <option value="{{ $key }}" @selected($status === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-4 flex items-center justify-between gap-3 border-t border-stone-100 pt-4">
            <div class="text-sm text-stone-500">
                @if(isset($machines))
                    <span class="font-semibold text-stone-900">{{ method_exists($machines, 'total') ? $machines->total() : $machines->count() }}</span> resultado(s)
                @endif
            </div>

            <div class="flex items-center gap-2">
                @if($q !== '' || (string)$category !== '' || $status !== '')
                    <x-ui.button :href="route('admin.machines.index')" variant="ghost" size="sm">Limpar</x-ui.button>
                @endif
                <x-ui.button type="submit" variant="dark" size="md">Filtrar</x-ui.button>
            </div>
        </div>
    </form>
</div>

<div class="mt-8 overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-card">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b border-stone-100 text-left text-stone-500">
                    <th class="px-6 py-4 font-semibold">Foto</th>
                    <th class="px-6 py-4 font-semibold">Nome</th>
                    <th class="px-6 py-4 font-semibold">Categoria</th>
                    <th class="px-6 py-4 font-semibold">Preço</th>
                    <th class="px-6 py-4 font-semibold">Estado</th>
                    <th class="px-6 py-4 font-semibold">Neg.</th>
                    <th class="px-6 py-4 font-semibold">Data</th>
                    <th class="px-6 py-4 text-right font-semibold">Ações</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-stone-100">
                @forelse(($machines ?? []) as $machine)
                    @php
                        $imgUrl = $machine->main_image?->thumb_url;

                        $mName       = $machine->name ?? '—';
                        $mCategory   = $machine->category->name ?? '—';
                        $mPrice      = $machine->price_formatted;
                        $mStatus     = $machine->status;
                        $mNegotiable = (bool) $machine->negotiable;
                        $createdAt   = $machine->created_at;

                        $updateStatusUrl = route('admin.machines.updateStatus', $machine);
                    @endphp

                    <tr class="transition hover:bg-stone-50">
                        <td class="px-6 py-4">
                            <div class="h-14 w-14 overflow-hidden rounded-xl bg-stone-100 ring-1 ring-stone-200">
                                @if($imgUrl)
                                    <img src="{{ $imgUrl }}" alt="" width="56" height="56" loading="lazy" decoding="async" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-stone-300">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                            <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                                            <path d="M3 16l5-5 4 4 3-3 6 6"></path>
                                            <circle cx="14" cy="8" r="1.4"></circle>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="font-semibold text-stone-900">{{ $mName }}</div>
                        </td>

                        <td class="px-6 py-4 text-stone-700">{{ $mCategory }}</td>

                        <td class="px-6 py-4 font-medium text-stone-900">{{ $mPrice ?? '—' }}</td>

                        <td class="px-6 py-4">
                            <span
                                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $badgeClass($mStatus) }}"
                                data-status-badge
                            >
                                {{ $machine->status_label }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            @if($mNegotiable)
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-brand-600 text-xs font-bold text-white" title="Negociável">N</span>
                            @else
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-stone-100 text-xs font-bold text-stone-400" title="Não negociável">—</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-stone-500">
                            @if($createdAt)
                                {{ \Carbon\Carbon::parse($createdAt)->format('d/m/Y') }}
                            @else
                                —
                            @endif
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.machines.edit', $machine) }}"
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-stone-200 bg-white text-stone-700 transition hover:border-brand-500 hover:text-brand-700"
                                   title="Editar">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 20h9"></path>
                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                                    </svg>
                                </a>

                                <div class="relative inline-flex">
                                    <button
                                        type="button"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-stone-200 bg-white text-stone-700 transition hover:border-brand-500 hover:text-brand-700"
                                        title="Alterar estado"
                                    >
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 21v-7"></path><path d="M4 10V3"></path>
                                            <path d="M12 21v-9"></path><path d="M12 8V3"></path>
                                            <path d="M20 21v-5"></path><path d="M20 12V3"></path>
                                            <path d="M2 14h4"></path><path d="M10 8h4"></path><path d="M18 16h4"></path>
                                        </svg>
                                    </button>

                                    <select
                                        class="absolute inset-0 h-9 w-9 cursor-pointer opacity-0"
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
                        <td colspan="8" class="px-6 py-16 text-center text-stone-500">
                            Ainda não tens máquinas. Clica em <span class="font-semibold text-stone-900">Nova Máquina</span>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(isset($machines) && method_exists($machines, 'links'))
        <div class="border-t border-stone-100 px-6 py-4">
            {{ $machines->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<script>
(function () {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const labelMap = @json(config('machines.statuses'));

    const classMap = {
        available: 'bg-emerald-100 text-emerald-700',
        reserved: 'bg-amber-100 text-amber-700',
        sold: 'bg-sky-100 text-sky-700',
        inactive: 'bg-stone-200 text-stone-600',
    };

    const allClasses = [
        'bg-emerald-100','text-emerald-700',
        'bg-amber-100','text-amber-700',
        'bg-sky-100','text-sky-700',
        'bg-stone-200','text-stone-600',
    ];

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
                    badge.classList.remove(...allClasses);
                    const cls = (classMap[value] || 'bg-stone-200 text-stone-600').split(' ');
                    cls.forEach((c) => badge.classList.add(c));
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
