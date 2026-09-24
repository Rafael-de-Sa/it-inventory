@extends('layouts.main_layout')

@section('content')
    <x-ui.card>
        <x-ui.card-header :title="'Equipamento — #' . $equipamento->id">
            <x-slot:subtitle>
                <x-ui.timestamps :model="$equipamento" />
            </x-slot:subtitle>
        </x-ui.card-header>

        <x-form.grid>
            <x-form.readonly id="equipamento_id" label="ID" :value="$equipamento->id" wrapper-class="md:col-span-3" />
            <x-form.active-toggle id="equipamento_ativo" :checked="$equipamento->ativo" disabled
                wrapper-class="md:col-span-3" />
            <x-form.readonly id="status" label="Status" :value="$equipamento->status_rotulo"
                wrapper-class="md:col-span-6" />

            <x-form.readonly id="tipo_nome" label="Tipo do Equipamento" :value="$equipamento->tipoEquipamento?->nome"
                wrapper-class="md:col-span-6" />
            <x-form.readonly id="patrimonio" label="Patrimônio" :value="$equipamento->patrimonio"
                wrapper-class="md:col-span-3" />
            <x-form.readonly id="numero_serie" label="Número de Série" :value="$equipamento->numero_serie"
                wrapper-class="md:col-span-3" />
        </x-form.grid>

        <x-form.fieldset legend="Aquisição">
            <x-form.grid>
                <x-form.readonly id="data_compra" label="Data da compra"
                    :value="$equipamento->data_compra?->format('d/m/Y')" wrapper-class="md:col-span-6" />
                <x-form.readonly id="valor_compra" label="Valor da compra"
                    :value="filled($equipamento->valor_compra) ? 'R$ ' . number_format((float) $equipamento->valor_compra, 2, ',', '.') : null"
                    wrapper-class="md:col-span-6" />
            </x-form.grid>
        </x-form.fieldset>

        <x-form.readonly id="descricao" label="Descrição" :value="$equipamento->descricao" multiline />

        <x-form.actions>
            <x-ui.button :href="route('equipamentos.index')" icon="fa-solid fa-arrow-left">Voltar</x-ui.button>

            <div class="flex items-center gap-3">
                <x-ui.button :href="route('relatorios.equipamentos.historico', $equipamento)" target="_blank"
                    rel="noopener noreferrer" icon="fa-solid fa-clock-rotate-left">Histórico</x-ui.button>
                <x-ui.button :href="route('equipamentos.edit', $equipamento)" icon="fa-solid fa-pen-to-square">Editar</x-ui.button>
                <x-ui.delete-button :action="route('equipamentos.destroy', $equipamento)"
                    :confirm="'Excluir o equipamento #' . $equipamento->id . ($equipamento->patrimonio ? ' (patrimônio ' . $equipamento->patrimonio . ')' : '') . '?'" />
            </div>
        </x-form.actions>
    </x-ui.card>
@endsection
