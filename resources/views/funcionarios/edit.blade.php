@extends('layouts.main_layout')

@section('content')
    <x-form.card id="funcionarioEditForm" :action="route('funcionarios.update', $funcionario)" method="PUT"
        :title="'Editar Funcionário — ' . $funcionario->nome_completo">
        <x-slot:subtitle>
            <x-ui.timestamps :model="$funcionario" />
        </x-slot:subtitle>

        @include('funcionarios.partials.campos')

        {{-- Ativo é só informativo (checkbox desabilitado não é enviado; quem envia é o hidden).
             Inativar é feito por "Registrar desligamento" na visualização. --}}
        <input type="hidden" name="ativo" value="{{ $funcionario->ativo ? 1 : 0 }}">
        <x-form.checkbox name="ativo" id="ativo_visual" label="Ativo" :checked="$funcionario->ativo" disabled />


        <x-form.actions>
            <x-ui.button :href="route('funcionarios.show', $funcionario)" icon="fa-solid fa-arrow-left">Cancelar</x-ui.button>
            <x-ui.button variant="primary" icon="fa-solid fa-floppy-disk">Salvar</x-ui.button>
        </x-form.actions>
    </x-form.card>
@endsection

@push('scripts')
    @vite('resources/js/funcionarios/funcionario-form.js')
@endpush
