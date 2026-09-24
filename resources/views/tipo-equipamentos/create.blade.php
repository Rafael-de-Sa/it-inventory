@extends('layouts.main_layout')

@section('content')
    <x-form.card id="tipoEquipamentoForm" :action="route('tipo-equipamentos.store')"
        title="Cadastro de Tipo de Equipamento" subtitle="Informe o nome do tipo.">

        <x-form.input name="nome" label="Nome" required autofocus autocomplete="off"
            placeholder="Ex.: Notebook, Desktop, Impressora..." help="Informe um nome claro, ex.: “Notebook”." />

        @include('tipo-equipamentos.partials.categoria', ['tipoEquipamento' => null])

        <x-form.actions align="end">
            <x-ui.button :href="route('tipo-equipamentos.index')" icon="fa-solid fa-arrow-left">Cancelar</x-ui.button>
            <x-ui.button variant="primary" icon="fa-solid fa-floppy-disk">Salvar</x-ui.button>
        </x-form.actions>
    </x-form.card>
@endsection
