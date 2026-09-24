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
        <div class="flex flex-col items-center space-y-6 py-4 text-center">
            <span class="inline-flex items-center gap-2 rounded-full bg-brand-50 px-3 py-1 text-xs font-medium text-brand-800 ring-1 ring-brand-600/20 ring-inset">
                <i class="fa-solid fa-laptop" aria-hidden="true"></i> Gestão de Ativos de TI
            </span>

            <h1 class="text-3xl font-semibold tracking-tight text-ink md:text-4xl">
                Boas-vindas ao <span class="text-brand-700 dark:text-brand-400">IT Inventory</span>
            </h1>

            <p class="max-w-2xl text-sm leading-relaxed text-ink-muted md:text-base">
                Utilize o sistema para cadastrar empresas, setores, funcionários, usuários e equipamentos,
                controlar as movimentações de entrega e devolução e gerar os termos de responsabilidade
                de forma centralizada e padronizada.
            </p>

            <div class="grid w-full max-w-3xl grid-cols-1 gap-4 pt-2 sm:grid-cols-3">
                @foreach ($areas as $area)
                    <div class="rounded-lg border border-line bg-surface-muted p-4 text-left">
                        <span class="mb-3 inline-flex h-9 w-9 items-center justify-center rounded-lg bg-brand-50 text-brand-700 ring-1 ring-brand-600/15 dark:text-brand-300">
                            <i class="{{ $area['icone'] }}" aria-hidden="true"></i>
                        </span>
                        <p class="text-sm font-semibold text-ink">{{ $area['titulo'] }}</p>
                        <p class="mt-1 text-sm leading-relaxed text-ink-muted">{{ $area['texto'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </x-ui.card>
@endsection
