@extends('layouts.main_layout')

@section('content')
    <div class="w-full flex justify-center">
        <div
            class="w-full max-w-5xl bg-green-900/40 border border-green-800 rounded-2xl shadow-lg px-6 py-10 md:px-10 md:py-14">

            <div class="flex flex-col items-center text-center space-y-6">
                {{-- Título / Boas-vindas --}}
                <h1 class="text-3xl md:text-4xl font-semibold tracking-wide leading-snug">
                    <span class="block">
                        Boas-vindas ao
                    </span>
                    <span class="block md:inline text-green-300">
                        IT Inventory
                    </span>
                    <span class="block md:inline">
                        – Sistema de Gestão de Ativos de TI
                    </span>
                </h1>

                {{-- Subtítulo opcional, mas ajuda a contextualizar --}}
                <p class="text-sm md:text-base text-green-100 max-w-2xl">
                    Utilize o sistema para cadastrar empresas, setores, funcionários, usuários e equipamentos,
                    controlar as movimentações de entrega e devolução e gerar os termos de responsabilidade
                    de forma centralizada e padronizada.
                </p>

                {{-- Cards de destaque --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 w-full max-w-3xl mt-4">
                    {{-- Card Cadastros --}}
                    <div class="rounded-xl border border-green-800/80 bg-green-900/40 px-4 py-3 text-left sm:text-center">
                        <div class="flex sm:flex-col items-center gap-3">
                            <i class="fa-solid fa-clipboard-list text-lg md:text-xl"></i>
                            <div class="space-y-1">
                                <p class="text-xs text-green-200 uppercase tracking-wide">Cadastros</p>
                                <p class="text-sm text-green-50">
                                    Gerencie empresas, setores, funcionários, usuários, tipos de equipamento e
                                    equipamentos.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Card Movimentações --}}
                    <div class="rounded-xl border border-green-800/80 bg-green-900/40 px-4 py-3 text-left sm:text-center">
                        <div class="flex sm:flex-col items-center gap-3">
                            <i class="fa-solid fa-right-left text-lg md:text-xl"></i>
                            <div class="space-y-1">
                                <p class="text-xs text-green-200 uppercase tracking-wide">Movimentações</p>
                                <p class="text-sm text-green-50">
                                    Registre entregas, devoluções e acompanhe o histórico de movimentações
                                    por funcionário, setor e equipamento.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Card Termos / Relatórios --}}
                    <div class="rounded-xl border border-green-800/80 bg-green-900/40 px-4 py-3 text-left sm:text-center">
                        <div class="flex sm:flex-col items-center gap-3">
                            <i class="fa-solid fa-file-signature text-lg md:text-xl"></i>
                            <div class="space-y-1">
                                <p class="text-xs text-green-200 uppercase tracking-wide">Termos</p>
                                <p class="text-sm text-green-50">
                                    Gere termos de responsabilidade e devolução padronizados para
                                    funcionários próprios e terceirizados.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
