@extends('layouts.main_layout')

@section('content')
    <x-form.card id="empresaForm" :action="route('empresas.store')" title="Cadastro de Empresa"
        subtitle="Preencha os dados abaixo." data-cep-endpoint="{{ route('empresas.cep', ['cep' => '00000000']) }}">

        @include('empresas.partials.campos')

        <x-form.actions align="end">
            <x-ui.button :href="route('empresas.index')" icon="fa-solid fa-arrow-left">Cancelar</x-ui.button>
            <x-ui.button variant="primary" icon="fa-solid fa-floppy-disk">Salvar</x-ui.button>
        </x-form.actions>
    </x-form.card>
@endsection

@push('scripts')
    @vite('resources/js/empresas/empresa-form.js')
@endpush
