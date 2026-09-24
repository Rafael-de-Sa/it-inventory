@extends('layouts.main_layout')

@section('content')
    <x-form.card id="setorEditForm" :action="route('setores.update', $setor)" method="PUT"
        :title="'Editar Setor — ' . $setor->nome">
        <x-slot:subtitle>
            <x-ui.timestamps :model="$setor" />
        </x-slot:subtitle>

        <x-form.grid>
            <x-form.readonly id="setor_id" label="ID" :value="$setor->id" wrapper-class="md:col-span-3" />
            <x-form.active-toggle :checked="$setor->ativo" wrapper-class="md:col-span-3" />
        </x-form.grid>

        <x-form.input name="nome" label="Nome do Setor" required maxlength="100" :value="$setor->nome"
            help="Informe um nome claro, ex.: “Tecnologia da Informação”." />

        <x-form.select name="empresa_id" label="Empresa" required placeholder="Selecione…" :value="$setor->empresa_id"
            :options="$opcoesEmpresas"
            :option-label="fn ($empresa) => $empresa->razao_social . ' — ' . \App\Support\Mask::cnpj($empresa->cnpj)"
            help="Vincule o setor à empresa." />

        <x-form.actions>
            <x-ui.button :href="route('setores.show', $setor)" icon="fa-solid fa-arrow-left">Cancelar</x-ui.button>
            <x-ui.button variant="primary" icon="fa-solid fa-floppy-disk">Salvar</x-ui.button>
        </x-form.actions>
    </x-form.card>
@endsection
