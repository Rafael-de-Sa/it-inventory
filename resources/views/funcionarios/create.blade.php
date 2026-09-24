@extends('layouts.main_layout')

@section('content')
    <x-form.card id="funcionarioForm" :action="route('funcionarios.store')" title="Cadastro de Funcionário"
        subtitle="Escolha a empresa e o setor, e preencha as informações do funcionário.">

        @include('funcionarios.partials.campos')

        <x-form.actions align="end">
            <x-ui.button :href="route('funcionarios.index')" icon="fa-solid fa-arrow-left">Cancelar</x-ui.button>
            <x-ui.button variant="primary" icon="fa-solid fa-floppy-disk">Salvar</x-ui.button>
        </x-form.actions>
    </x-form.card>
@endsection

@push('scripts')
    @vite('resources/js/funcionarios/funcionario-form.js')
@endpush
