@extends('layouts.main_layout')

@section('content')
    <x-ui.card>
        <x-ui.card-header :title="'Setor — ' . $setor->nome">
            <x-slot:subtitle>
                <x-ui.timestamps :model="$setor" />
            </x-slot:subtitle>
        </x-ui.card-header>

        <x-form.grid>
            <x-form.readonly id="setor_id" label="ID" :value="$setor->id" wrapper-class="md:col-span-3" />
            <x-form.active-toggle id="setor_ativo" :checked="$setor->ativo" disabled wrapper-class="md:col-span-3" />
        </x-form.grid>

        <x-form.readonly id="nome" label="Nome do Setor" :value="$setor->nome" />

        <x-form.fieldset legend="Empresa">
            <x-form.grid>
                <x-form.readonly id="empresa_nome" label="Nome da Empresa" :value="$setor->empresa?->razao_social"
                    wrapper-class="md:col-span-8" />
                <x-form.readonly id="empresa_cnpj" label="CNPJ" :value="\App\Support\Mask::cnpj($setor->empresa?->cnpj)"
                    wrapper-class="md:col-span-4" />
            </x-form.grid>
        </x-form.fieldset>

        <x-form.actions>
            <x-ui.button :href="route('setores.index')" icon="fa-solid fa-arrow-left">Voltar</x-ui.button>

            <div class="flex items-center gap-3">
                <x-ui.button :href="route('setores.edit', $setor)" icon="fa-solid fa-pen-to-square">Editar</x-ui.button>
                <x-ui.delete-button :action="route('setores.destroy', $setor)"
                    :confirm="'Excluir o setor ' . $setor->nome . '?'" />
            </div>
        </x-form.actions>
    </x-ui.card>
@endsection
