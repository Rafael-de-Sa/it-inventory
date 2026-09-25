@extends('layouts.main_layout')

@section('content')
    <x-form.card id="tipoEquipamentoEditForm" :action="route('tipo-equipamentos.update', $tipoEquipamento)" method="PUT"
        :title="'Editar Tipo de Equipamento — ' . $tipoEquipamento->nome">
        <x-slot:subtitle>
            <x-ui.timestamps :model="$tipoEquipamento" />
        </x-slot:subtitle>

        <x-form.grid>
            <x-form.readonly id="tipo_id" label="ID" :value="$tipoEquipamento->id" wrapper-class="md:col-span-3" />
            <x-form.active-toggle :checked="$tipoEquipamento->ativo" wrapper-class="md:col-span-3" />
        </x-form.grid>

        <x-form.input name="nome" label="Nome" required autocomplete="off" :value="$tipoEquipamento->nome"
            placeholder="Ex.: Notebook, Desktop, Impressora..." help="Informe um nome claro, ex.: “Notebook”." />

        @include('tipo-equipamentos.partials.categoria')

        <x-form.actions>
            <x-ui.button :href="route('tipo-equipamentos.show', $tipoEquipamento)" icon="fa-solid fa-arrow-left">
                Cancelar
            </x-ui.button>
            <x-ui.button variant="primary" icon="fa-solid fa-floppy-disk">Salvar</x-ui.button>
        </x-form.actions>
    </x-form.card>
@endsection
