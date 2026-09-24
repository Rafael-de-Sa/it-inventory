{{--
    <x-table :headers="['ID', 'Nome', 'Ações']">
        @forelse ($itens as $item)
            <x-table.row> <x-table.cell>{{ $item->id }}</x-table.cell> ... </x-table.row>
        @empty
            <x-table.empty :colspan="3">Nenhum registro encontrado.</x-table.empty>
        @endforelse
    </x-table>

    `title`/`hint` exibem uma barra acima da tabela. Cabeçalhos customizados vão no slot `head`
    (renderizado depois de `headers`). Atributos extras (id, data-*) vão para o invólucro.
--}}
@props([
    'headers' => [],
    'title' => null,
    'hint' => null,
])

<div {{ $attributes->class('overflow-hidden rounded-xl border border-line bg-surface shadow-sm') }}>
    @if ($title)
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-line px-4 py-3">
            <span class="text-sm font-semibold text-ink">{{ $title }}</span>
            @if ($hint)
                <span class="text-xs text-ink-muted">{{ $hint }}</span>
            @endif
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full table-auto text-sm text-ink">
            <thead class="border-b border-line bg-surface-muted text-center text-xs font-semibold tracking-wide text-ink-muted uppercase">
                <tr>
                    @foreach ($headers as $header)
                        <th scope="col" class="px-4 py-3 font-semibold">{{ $header }}</th>
                    @endforeach
                    {{ $head ?? '' }}
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>
