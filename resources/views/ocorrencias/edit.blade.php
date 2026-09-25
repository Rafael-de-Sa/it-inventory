@extends('layouts.main_layout')

@section('content')
    <x-form.card :action="route('ocorrencias.update', $ocorrencia)" method="PUT" size="lg"
        :title="'Editar ocorrência — #' . $ocorrencia->id"
        subtitle="Corrige os dados da ocorrência. Para encerrar ou reabrir, use os botões na tela da ocorrência.">
        @include('ocorrencias.partials.campos')

        <x-form.actions class="pt-2">
            <x-ui.button :href="route('ocorrencias.show', $ocorrencia)" icon="fa-solid fa-arrow-left" class="text-sm">Cancelar</x-ui.button>
            <x-ui.button variant="primary" icon="fa-solid fa-floppy-disk" class="text-sm">Salvar</x-ui.button>
        </x-form.actions>
    </x-form.card>
@endsection

@push('scripts')
    @vite('resources/js/ocorrencias/ocorrencia-form.js')
@endpush
