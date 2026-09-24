@extends('layouts.main_layout')

@section('content')
    <x-form.card id="equipamentoForm" :action="route('equipamentos.store')" title="Cadastro de Equipamento"
        subtitle="Preencha os dados do equipamento.">

        <x-form.grid>
            <x-form.select name="tipo_equipamento_id" label="Tipo de Equipamento" required placeholder="Selecione..."
                :options="$opcoesTipos" wrapper-class="md:col-span-7" />
            <x-form.select name="status" label="Status" required :options="$listaStatus" value="disponivel"
                wrapper-class="md:col-span-5" />

            <x-form.input name="patrimonio" label="Patrimônio" maxlength="50" wrapper-class="md:col-span-6" />
            <x-form.input name="numero_serie" label="Número de Série" maxlength="100" wrapper-class="md:col-span-6" />

            <x-form.input type="date" name="data_compra" label="Data da Compra" wrapper-class="md:col-span-6" />
            {{-- Aceita formato brasileiro; o StoreEquipamentoRequest normaliza "1.234,56" para 1234.56 --}}
            <x-form.input name="valor_compra" label="Valor da Compra" inputmode="decimal" placeholder="Ex.: 1.234,56"
                wrapper-class="md:col-span-6" />
        </x-form.grid>

        <x-form.textarea name="descricao" label="Descrição" required rows="4" placeholder="Descrição do equipamento" />

        <x-form.actions>
            <x-ui.button :href="route('equipamentos.index')" icon="fa-solid fa-arrow-left">Cancelar</x-ui.button>
            <x-ui.button variant="primary" icon="fa-solid fa-floppy-disk">Salvar</x-ui.button>
        </x-form.actions>
    </x-form.card>
@endsection
