@extends('layouts.main_layout')

@section('content')
    <x-form.card id="empresaEditForm" :action="route('empresas.update', $empresa)" method="PUT"
        :title="'Empresa — ' . $empresa->razao_social"
        data-cep-endpoint="{{ route('empresas.cep', ['cep' => '00000000']) }}">
        <x-slot:subtitle>
            <x-ui.timestamps :model="$empresa" />
        </x-slot:subtitle>

        <x-form.grid>
            <x-form.readonly id="empresa_id" label="ID" :value="$empresa->id" wrapper-class="md:col-span-3" />
            <x-form.active-toggle label="Ativa" active-label="Ativa" inactive-label="Inativa" :checked="$empresa->ativo"
                wrapper-class="md:col-span-3" />
        </x-form.grid>

        @include('empresas.partials.campos', ['empresa' => $empresa])

        <x-form.actions>
            <x-ui.button :href="route('empresas.show', $empresa)" icon="fa-solid fa-arrow-left">Cancelar</x-ui.button>
            <x-ui.button variant="primary" icon="fa-solid fa-floppy-disk">Salvar</x-ui.button>
        </x-form.actions>
    </x-form.card>
@endsection

@push('scripts')
    @vite('resources/js/empresas/empresa-form.js')
@endpush
