@extends('layouts.main_layout')

@section('content')
    <x-ui.card>
        <x-ui.card-header :title="'Tipo de Equipamento — ' . $tipoEquipamento->nome">
            <x-slot:subtitle>
                <x-ui.timestamps :model="$tipoEquipamento" />
            </x-slot:subtitle>
        </x-ui.card-header>

        <x-form.grid>
            <x-form.readonly id="tipo_id" label="ID" :value="$tipoEquipamento->id" wrapper-class="md:col-span-3" />
            <x-form.active-toggle id="tipo_ativo" :checked="$tipoEquipamento->ativo" disabled
                wrapper-class="md:col-span-3" />
        </x-form.grid>

        <x-form.readonly id="nome" label="Nome" :value="$tipoEquipamento->nome" />

        <x-form.actions>
            <x-ui.button :href="route('tipo-equipamentos.index')" icon="fa-solid fa-arrow-left">Voltar</x-ui.button>

            <div class="flex items-center gap-3">
                <x-ui.button :href="route('tipo-equipamentos.edit', $tipoEquipamento)" icon="fa-solid fa-pen-to-square">
                    Editar
                </x-ui.button>
                <x-ui.delete-button :action="route('tipo-equipamentos.destroy', $tipoEquipamento)"
                    confirm="Tem certeza que deseja remover este tipo?" />
            </div>
        </x-form.actions>
    </x-ui.card>
@endsection
