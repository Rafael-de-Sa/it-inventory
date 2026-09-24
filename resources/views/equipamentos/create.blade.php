@extends('layouts.main_layout')

@section('content')
    <x-form.card id="equipamentoForm" :action="route('equipamentos.store')" size="lg" title="Cadastro de Equipamento"
        subtitle="Preencha os dados do equipamento. A ficha técnica muda conforme o tipo escolhido.">

        @include('equipamentos.partials.campos')

        <x-form.actions>
            <x-ui.button :href="route('equipamentos.index')" icon="fa-solid fa-arrow-left">Cancelar</x-ui.button>
            <x-ui.button variant="primary" icon="fa-solid fa-floppy-disk">Salvar</x-ui.button>
        </x-form.actions>
    </x-form.card>
@endsection
