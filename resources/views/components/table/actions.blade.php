{{--
    Ações da linha: exibir, editar e excluir. Passe só as rotas que a linha precisa.
    <x-table.actions :show="route('x.show', $x)" :edit="route('x.edit', $x)" :destroy="route('x.destroy', $x)" confirm="..." />
    Ações extras podem ir no slot.
--}}
@props([
    'show' => null,
    'edit' => null,
    'destroy' => null,
    'confirm' => 'Tem certeza que deseja excluir este registro?',
])

<x-table.cell>
    <div class="inline-flex items-center gap-2">
        @if ($show)
            <x-ui.icon-button :href="$show" icon="fa-solid fa-eye" label="Exibir" />
        @endif
        @if ($edit)
            <x-ui.icon-button :href="$edit" icon="fa-solid fa-pen-to-square" label="Editar" />
        @endif

        {{ $slot }}

        @if ($destroy)
            <x-ui.delete-button :action="$destroy" :confirm="$confirm" icon-only />
        @endif
    </div>
</x-table.cell>
