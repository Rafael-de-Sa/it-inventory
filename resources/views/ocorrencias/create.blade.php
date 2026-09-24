@extends('layouts.main_layout')

@section('content')
    <x-form.card :action="route('ocorrencias.store')" size="lg" title="Registrar ocorrência"
        subtitle="Problema reportado em um equipamento, do registro até a liberação pela TI.">
        @include('ocorrencias.partials.campos')

        <x-form.actions class="pt-2">
            <x-ui.button :href="route('ocorrencias.index')" icon="fa-solid fa-arrow-left" class="text-sm">Cancelar</x-ui.button>
            <x-ui.button variant="primary" icon="fa-solid fa-floppy-disk" class="text-sm">Registrar</x-ui.button>
        </x-form.actions>
    </x-form.card>
@endsection

@push('scripts')
    @vite('resources/js/ocorrencias/ocorrencia-form.js')
@endpush
