{{--
    <x-table :headers="['ID', 'Nome', 'Ações']">
        @forelse ($itens as $item)
            <x-table.row> <x-table.cell>{{ $item->id }}</x-table.cell> ... </x-table.row>
        @empty
            <x-table.empty :colspan="3">Nenhum registro encontrado.</x-table.empty>
        @endforelse
    </x-table>
--}}
@props(['headers' => []])

<div {{ $attributes->class('overflow-x-auto rounded-xl border border-green-800') }}>
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
