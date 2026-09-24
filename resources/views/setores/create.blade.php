@extends('layouts.main_layout')

@section('content')
    <x-form.card id="setorForm" :action="route('setores.store')" title="Cadastro de Setor"
        subtitle="Vincule o setor a uma empresa e informe o nome.">

        <x-form.select name="empresa_id" label="Empresa" required placeholder="Selecione..." :options="$empresas"
            :option-label="fn ($empresa) => $empresa->id . ' - ' . \App\Support\Mask::cnpj($empresa->cnpj) . ' - ' . $empresa->razao_social"
            help="Escolha a empresa do setor." />

        <x-form.input name="nome" label="Nome do Setor" required maxlength="50"
            placeholder="Ex.: Financeiro ou Departamento Pessoal"
            help="Informe um nome claro, ex.: “Tecnologia da Informação”." />

        <x-form.actions align="end">
            <x-ui.button :href="route('setores.index')" icon="fa-solid fa-arrow-left">Cancelar</x-ui.button>
            <x-ui.button variant="primary" icon="fa-solid fa-floppy-disk">Salvar</x-ui.button>
        </x-form.actions>
    </x-form.card>
@endsection
