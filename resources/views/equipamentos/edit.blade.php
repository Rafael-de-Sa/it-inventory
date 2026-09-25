@extends('layouts.main_layout')

@section('content')
    <x-form.card id="equipamentoEditForm" :action="route('equipamentos.update', $equipamento)" method="PUT" size="lg"
        :title="'Editar Equipamento — #' . $equipamento->id">
        <x-slot:subtitle>
            <x-ui.timestamps :model="$equipamento" />
        </x-slot:subtitle>

        @if (blank($equipamento->fabricante) || blank($equipamento->modelo))
            <x-ui.alert variant="info" icon="fa-solid fa-circle-info" title="Cadastro anterior à versão 2.0">
                <p>Informe fabricante, modelo e a ficha técnica. A descrição antiga foi mantida em "Observação".</p>
            </x-ui.alert>
        @endif

        @include('equipamentos.partials.campos')

        <x-form.actions>
            <x-ui.button :href="route('equipamentos.show', $equipamento)" icon="fa-solid fa-arrow-left">Cancelar</x-ui.button>
            <x-ui.button variant="primary" icon="fa-solid fa-floppy-disk">Salvar</x-ui.button>
        </x-form.actions>
    </x-form.card>
@endsection
