@extends('layouts.main_layout')

@section('content')
    @php
        $areas = [
            [
                'icone' => 'fa-solid fa-clipboard-list',
                'titulo' => 'Cadastros',
                'texto' => 'Gerencie empresas, setores, funcionários, usuários, tipos de equipamento e equipamentos.',
            ],
            [
                'icone' => 'fa-solid fa-right-left',
                'titulo' => 'Movimentações',
                'texto' => 'Registre entregas, devoluções e acompanhe o histórico de movimentações por funcionário, setor e equipamento.',
            ],
            [
                'icone' => 'fa-solid fa-file-signature',
                'titulo' => 'Termos',
                'texto' => 'Gere termos de responsabilidade e devolução padronizados para funcionários próprios e terceirizados.',
            ],
        ];
    @endphp

    <x-ui.card size="lg">
        <div class="flex flex-col items-center space-y-6 text-center">
            <h1 class="text-3xl font-semibold leading-snug tracking-wide md:text-4xl">
                <span class="block">Boas-vindas ao</span>
                <span class="block text-green-300 md:inline">IT Inventory</span>
                <span class="block md:inline">– Sistema de Gestão de Ativos de TI</span>
            </h1>

            <p class="max-w-2xl text-sm text-green-100 md:text-base">
                Utilize o sistema para cadastrar empresas, setores, funcionários, usuários e equipamentos,
                controlar as movimentações de entrega e devolução e gerar os termos de responsabilidade
                de forma centralizada e padronizada.
            </p>

            <div class="mt-4 grid w-full max-w-3xl grid-cols-1 gap-4 sm:grid-cols-3">
                @foreach ($areas as $area)
                    <div class="rounded-xl border border-green-800/80 bg-green-900/40 px-4 py-3 text-left sm:text-center">
                        <div class="flex items-center gap-3 sm:flex-col">
                            <i class="{{ $area['icone'] }} text-lg md:text-xl" aria-hidden="true"></i>
                            <div class="space-y-1">
                                <p class="text-xs uppercase tracking-wide text-green-200">{{ $area['titulo'] }}</p>
                                <p class="text-sm text-green-50">{{ $area['texto'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-ui.card>
@endsection
