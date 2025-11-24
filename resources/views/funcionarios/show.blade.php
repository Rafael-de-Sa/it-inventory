@extends('layouts.main_layout')

@section('content')
    <div class="w-full flex justify-center">
        <div class="w-full max-w-4xl bg-green-900/40 border border-green-800 rounded-2xl shadow-lg p-6 md:p-8 space-y-6">

            {{-- Cabeçalho --}}
            <header class="space-y-1">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-2xl font-semibold tracking-wide">
                        Funcionário - {{ trim(($funcionario->nome ?? '') . ' ' . ($funcionario->sobrenome ?? '')) ?: '—' }}
                    </h2>

                </div>

                <p class="text-xs text-green-200">
                    Criado em: {{ optional($funcionario->criado_em)->format('d/m/Y H:i') ?? '—' }} ·
                    Atualizado em: {{ optional($funcionario->atualizado_em)->format('d/m/Y H:i') ?? '—' }}
                </p>
            </header>

            <hr class="border-green-800/50">

            @php
                $empresa = optional($funcionario->setor)->empresa;
            @endphp

            {{-- Dados principais --}}
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                {{-- ID --}}
                <div class="md:col-span-3">
                    <label class="block mb-1 text-sm font-medium text-green-100">ID</label>
                    <input type="text" value="{{ $funcionario->id }}"
                        class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black cursor-default"
                        disabled readonly>
                </div>

                {{-- ID --}}
                <div class="md:col-span-3">
                    <label for="equipamento_ativo" class="block mb-1 text-sm font-medium text-green-100">Ativo</label>
                    <div class="h-[42px] flex items-center gap-3">
                        <input id="equipamento_ativo" type="checkbox" disabled @checked($funcionario->ativo)
                            class="h-5 w-5 rounded border-green-700 bg-gray-300 text-green-700
                               cursor-default focus:outline-none focus:ring-0 focus:ring-offset-0">

                        @if ($funcionario->ativo)
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium
                                   border border-green-600/60 bg-green-600/15 text-green-100
                                   ring-1 ring-inset ring-green-400/10 shadow-sm">
                                <i class="fa-solid fa-check-circle text-[10px]"></i>
                                Ativo
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium
                                   border border-gray-500/60 bg-gray-500/15 text-gray-200/80
                                   ring-1 ring-inset ring-gray-400/10 shadow-sm">
                                <i class="fa-solid fa-circle-xmark text-[10px]"></i>
                                Inativo
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Matrícula --}}
                <div class="md:col-span-3">
                    <label class="block mb-1 text-sm font-medium text-green-100">Matrícula</label>
                    <input type="text" value="{{ $funcionario->matricula ?? '—' }}"
                        class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black cursor-default"
                        disabled readonly>
                </div>

                {{-- Terceirizado --}}
                <div class="md:col-span-3">
                    <label for="equipamento_ativo"
                        class="block mb-1 text-sm font-medium text-green-100">Terceirizado</label>
                    <div class="h-[42px] flex items-center gap-3">
                        <input id="equipamento_ativo" type="checkbox" disabled @checked($funcionario->terceirizado)
                            class="h-5 w-5 rounded border-green-700 bg-gray-300 text-green-700
                               cursor-default focus:outline-none focus:ring-0 focus:ring-offset-0">

                        @if ($funcionario->terceirizado)
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium
                                   border border-green-600/60 bg-green-600/15 text-green-100
                                   ring-1 ring-inset ring-green-400/10 shadow-sm">
                                <i class="fa-solid fa-check-circle text-[10px]"></i>
                                Sim
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium
                                   border border-gray-500/60 bg-gray-500/15 text-gray-200/80
                                   ring-1 ring-inset ring-gray-400/10 shadow-sm">
                                <i class="fa-solid fa-circle-xmark text-[10px]"></i>
                                Não
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Desligado em --}}
                <div class="md:col-span-3">
                    <label class="block mb-1 text-sm font-medium text-green-100">Desligado em</label>
                    <input type="text" value="{{ optional($funcionario->desligado_em)->format('d/m/Y H:i') ?? '—' }}"
                        class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black cursor-default"
                        disabled readonly>
                </div>

                {{-- Nome --}}
                <div class="md:col-span-6">
                    <label class="block mb-1 text-sm font-medium text-green-100">Nome</label>
                    <input type="text" value="{{ $funcionario->nome ?? '—' }}"
                        class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black cursor-default"
                        disabled readonly>
                </div>

                {{-- Sobrenome --}}
                <div class="md:col-span-6">
                    <label class="block mb-1 text-sm font-medium text-green-100">Sobrenome</label>
                    <input type="text" value="{{ $funcionario->sobrenome ?? '—' }}"
                        class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black cursor-default"
                        disabled readonly>
                </div>

                {{-- CPF (máscara helper) --}}
                <div class="md:col-span-4">
                    <label class="block mb-1 text-sm font-medium text-green-100">CPF</label>
                    <input type="text" value="{{ \App\Support\Mask::cpf($funcionario->cpf) ?: '—' }}"
                        class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black cursor-default"
                        disabled readonly>
                </div>

                {{-- Telefone --}}
                <div class="md:col-span-4">
                    <label class="block mb-1 text-sm font-medium text-green-100">Telefone</label>
                    <input type="text" value="{{ \App\Support\Mask::telefone($funcionario->telefone) ?? '—' }}"
                        class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black cursor-default"
                        disabled readonly>
                </div>

                {{-- Setor --}}
                <div class="md:col-span-4">
                    <label class="block mb-1 text-sm font-medium text-green-100">Setor</label>
                    <input type="text" value="{{ optional($funcionario->setor)->nome ?? '—' }}"
                        class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black cursor-default"
                        disabled readonly>
                </div>

                {{-- Empresa --}}
                <div class="md:col-span-8">
                    <label class="block mb-1 text-sm font-medium text-green-100">Empresa</label>
                    <input type="text" value="{{ $empresa->nome_fantasia ?? '—' }}"
                        class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black cursor-default"
                        disabled readonly>
                </div>

                {{-- CNPJ (máscara helper) --}}
                <div class="md:col-span-4">
                    <label class="block mb-1 text-sm font-medium text-green-100">CNPJ da Empresa</label>
                    <input type="text" value="{{ \App\Support\Mask::cnpj($empresa->cnpj ?? null) ?: '—' }}"
                        class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black cursor-default"
                        disabled readonly>
                </div>
            </div>

            {{-- Rodapé --}}
            {{-- Rodapé - Ações padronizadas --}}
            <div class="mt-6 border-t border-green-800 pt-4">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    {{-- Voltar --}}
                    <a href="{{ route('funcionarios.index') }}"
                        class="px-4 py-2 rounded-lg border border-green-700 hover:bg-green-800/40 inline-flex items-center gap-2 text-sm"
                        title="Voltar" aria-label="Voltar">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Voltar</span>
                    </a>

                    <div class="flex flex-wrap items-center gap-2 md:gap-3">
                        {{-- Relatório --}}
                        <a href="{{ route('relatorios.funcionarios.equipamentos', $funcionario) }}" target="_blank"
                            rel="noopener noreferrer"
                            class="px-4 py-2 rounded-lg border border-green-700 hover:bg-green-800/40 inline-flex items-center gap-2 text-sm">
                            <i class="fa-solid fa-file-pdf"></i>
                            <span>Relatório de equipamentos</span>
                        </a>

                        {{-- Desligar funcionário --}}
                        @if ($podeMostrarBotaoDesligar)
                            <form method="POST" action="{{ route('funcionarios.desligar', $funcionario->id) }}"
                                onsubmit="return confirm('Confirmar o desligamento deste funcionário?');" class="inline">
                                @csrf
                                <button type="submit"
                                    class="cursor-pointer px-4 py-2 rounded-lg border border-amber-600 text-amber-100 hover:bg-amber-900/30 inline-flex items-center gap-2 text-sm"
                                    aria-label="Registrar desligamento">
                                    <i class="fa-solid fa-user-slash"></i>
                                    <span>Registrar desligamento</span>
                                </button>
                            </form>
                        @endif

                        {{-- Editar --}}
                        <a href="{{ route('funcionarios.edit', $funcionario->id) }}"
                            class="px-4 py-2 rounded-lg border border-green-700 hover:bg-green-800/40 inline-flex items-center gap-2 text-sm"
                            title="Editar" aria-label="Editar">
                            <i class="fa-solid fa-pen-to-square"></i>
                            <span>Editar</span>
                        </a>

                        {{-- Excluir --}}
                        @if ($podeMostrarBotaoExcluir)
                            <form method="POST" action="{{ route('funcionarios.destroy', $funcionario->id) }}"
                                onsubmit="return confirm('Excluir o funcionário?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="cursor-pointer px-4 py-2 rounded-lg border border-red-700 text-red-200 hover:bg-red-900/30 inline-flex items-center gap-2 text-sm"
                                    aria-label="Excluir">
                                    <i class="fa-solid fa-trash"></i>
                                    <span>Excluir</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Mensagem de pendências --}}
                @if (!$podeRealizarDesligamento && !$funcionarioPertenceAoUsuarioLogado)
                    <div
                        class="mt-3 rounded-lg border border-amber-500/70 bg-amber-950/40 px-4 py-3 text-xs text-amber-100 space-y-1">
                        <p class="font-semibold flex items-center gap-2">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>Ações de desligamento e exclusão indisponíveis devido a pendências:</span>
                        </p>

                        <ul class="list-disc list-inside space-y-0.5">
                            @if ($restricoesDesligamento['ja_desligado'] ?? false)
                                <li>Funcionário já está marcado como desligado.</li>
                            @endif

                            @if ($restricoesDesligamento['equipamentos_em_uso'] ?? false)
                                <li>Há equipamentos sob responsabilidade deste funcionário.</li>
                            @endif

                            @if ($restricoesDesligamento['termos_responsabilidade_pendentes'] ?? false)
                                <li>Existem termos de responsabilidade pendentes de upload.</li>
                            @endif

                            @if ($restricoesDesligamento['termos_devolucao_pendentes'] ?? false)
                                <li>Existem termos de devolução pendentes de upload.</li>
                            @endif
                        </ul>
                    </div>
                @endif

                {{-- Mensagem para o próprio usuário logado --}}
                @if ($funcionarioPertenceAoUsuarioLogado)
                    <div
                        class="mt-3 rounded-lg border border-sky-500/70 bg-sky-950/40 px-4 py-3 text-xs text-sky-100 space-y-1">
                        <p class="font-semibold flex items-center gap-2">
                            <i class="fa-solid fa-circle-info"></i>
                            <span>Você não pode realizar o próprio desligamento ou exclusão de usuário.</span>
                        </p>
                    </div>
                @endif
            </div>


        </div>
    </div>
@endsection
