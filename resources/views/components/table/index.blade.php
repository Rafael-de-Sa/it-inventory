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

<div {{ $attributes->class('overflow-hidden rounded-xl border border-green-800') }}>
    @if ($title)
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-green-800/60 px-4 py-2">
            <span class="text-sm font-medium text-green-100">{{ $title }}</span>
            @if ($hint)
                <span class="text-xs text-green-200/80">{{ $hint }}</span>
            @endif
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full table-auto text-sm">
            <thead class="bg-green-900/60 text-center text-green-100">
                <tr>
                    @foreach ($headers as $header)
                        <th class="px-4 py-2">{{ $header }}</th>
                    @endforeach
                    {{ $head ?? '' }}
                </tr>
            </thead>
            <tbody class="bg-green-950/10">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>
