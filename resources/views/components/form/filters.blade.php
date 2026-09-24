{{--
    Formulário GET de filtros das listagens, com Voltar / Aplicar / Limpar.
    <x-form.filters :reset="route('setores.index')"> ...campos com wrapper-class="md:col-span-N"... </x-form.filters>
--}}
@props([
    'reset',
    'back' => null,
])

<form method="GET" {{ $attributes->class('grid gap-3 rounded-xl border border-green-800 bg-green-900/10 p-3 md:grid-cols-12') }}>
    {{ $slot }}

    <div class="flex flex-wrap items-end justify-between gap-2 md:col-span-12">
        <x-ui.button :href="$back ?? route('/')" icon="fa-solid fa-arrow-left">Voltar</x-ui.button>

        <div class="flex items-end gap-2">
            <x-ui.button variant="soft" icon="fa-solid fa-filter">Aplicar</x-ui.button>
            <x-ui.button :href="$reset" icon="fa-solid fa-rotate-left">Limpar</x-ui.button>
        </div>
    </div>
</form>
