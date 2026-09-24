@extends('layouts.main_layout')

@section('content')
    <x-ui.card>
        <x-ui.card-header :title="'Empresa — ' . $empresa->razao_social">
            <x-slot:subtitle>
                <x-ui.timestamps :model="$empresa" />
            </x-slot:subtitle>
        </x-ui.card-header>

        <x-form.grid>
            <x-form.readonly id="empresa_id" label="ID" :value="$empresa->id" wrapper-class="md:col-span-3" />
            <x-form.active-toggle id="empresa_ativa" label="Ativa" active-label="Ativa" inactive-label="Inativa"
                :checked="$empresa->ativo" disabled wrapper-class="md:col-span-3" />
        </x-form.grid>

        <x-form.readonly id="nome_fantasia" label="Nome Fantasia" :value="$empresa->nome_fantasia" />
        <x-form.readonly id="razao_social" label="Razão Social" :value="$empresa->razao_social" />
        <x-form.readonly id="cnpj" label="CNPJ" :value="\App\Support\Mask::cnpj($empresa->cnpj)" />

        <x-form.fieldset legend="Endereço">
            <x-form.grid>
                <x-form.readonly id="cep" label="CEP" :value="\App\Support\Mask::cep($empresa->cep)"
                    wrapper-class="md:col-span-3" />
                <x-form.readonly id="logradouro" label="Logradouro" :value="$empresa->logradouro" wrapper-class="md:col-span-9" />
                <x-form.readonly id="numero" label="Número" :value="$empresa->numero" wrapper-class="md:col-span-3" />
                <x-form.readonly id="bairro" label="Bairro" :value="$empresa->bairro" wrapper-class="md:col-span-4" />
                <x-form.readonly id="complemento" label="Complemento" :value="$empresa->complemento"
                    wrapper-class="md:col-span-5" />
                <x-form.readonly id="cidade" label="Cidade" :value="$empresa->cidade" wrapper-class="md:col-span-8" />
                <x-form.readonly id="estado" label="Estado" :value="$empresa->estado" wrapper-class="md:col-span-4" />
            </x-form.grid>
        </x-form.fieldset>

        <x-form.readonly id="email" label="E-mail" :value="$empresa->email" />
        <x-form.readonly id="telefone" label="Telefone" :value="\App\Support\Mask::telefone($empresa->telefone)" />

        <x-form.actions>
            <x-ui.button :href="route('empresas.index')" icon="fa-solid fa-arrow-left">Voltar</x-ui.button>

            <div class="flex items-center gap-3">
                <x-ui.button :href="route('empresas.edit', $empresa)" icon="fa-solid fa-pen-to-square">Editar</x-ui.button>
                <x-ui.delete-button :action="route('empresas.destroy', $empresa)"
                    :confirm="'Excluir a empresa ' . $empresa->razao_social . '?'" />
            </div>
        </x-form.actions>
    </x-ui.card>
@endsection
