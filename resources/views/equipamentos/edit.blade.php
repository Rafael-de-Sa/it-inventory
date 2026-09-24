@extends('layouts.main_layout')

@section('content')
    <x-form.card id="equipamentoEditForm" :action="route('equipamentos.update', $equipamento)" method="PUT"
        :title="'Editar Equipamento — #' . $equipamento->id">
        <x-slot:subtitle>
            <x-ui.timestamps :model="$equipamento" />
        </x-slot:subtitle>

        <x-form.grid>
            <x-form.readonly id="equipamento_id" label="ID" :value="$equipamento->id" wrapper-class="md:col-span-3" />
            <x-form.select name="status" label="Status" required :options="\App\Models\Equipamento::STATUS"
                :value="$equipamento->status" wrapper-class="md:col-span-4" />

            <x-form.select name="tipo_equipamento_id" label="Tipo do Equipamento" required placeholder="Selecione..."
                :options="$opcoesTiposEquipamento" :value="$equipamento->tipo_equipamento_id"
                wrapper-class="md:col-span-6" />
            <x-form.input name="patrimonio" label="Patrimônio" :value="$equipamento->patrimonio"
                wrapper-class="md:col-span-3" />
            <x-form.input name="numero_serie" label="Número de Série" :value="$equipamento->numero_serie"
                wrapper-class="md:col-span-3" />
        </x-form.grid>

        <x-form.fieldset legend="Aquisição">
            <x-form.grid>
                <x-form.input type="date" name="data_compra" label="Data da compra"
                    :value="$equipamento->data_compra?->format('Y-m-d')" wrapper-class="md:col-span-6" />
                <x-form.input type="number" name="valor_compra" label="Valor da compra" step="0.01" inputmode="decimal"
                    :value="$equipamento->valor_compra" wrapper-class="md:col-span-6" />
            </x-form.grid>
        </x-form.fieldset>

        <x-form.textarea name="descricao" label="Descrição" required rows="4" :value="$equipamento->descricao" />

        <x-form.actions>
            <x-ui.button :href="route('equipamentos.show', $equipamento)" icon="fa-solid fa-arrow-left">Cancelar</x-ui.button>
            <x-ui.button variant="primary" icon="fa-solid fa-floppy-disk">Salvar</x-ui.button>
        </x-form.actions>
    </x-form.card>
@endsection
