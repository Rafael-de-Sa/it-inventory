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

            {{-- Rodapé - Ações padronizadas --}}
            <div class="flex items-center justify-between pt-2">
                {{-- Voltar --}}
                <a href="{{ route('funcionarios.index') }}"
                    class="px-4 py-2 rounded-lg border border-green-700 hover:bg-green-800/40 inline-flex items-center gap-2"
                    title="Voltar" aria-label="Voltar">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Voltar</span>
                </a>

                <div class="flex items-center gap-3">
                    {{-- relatório --}}
                    <a href="{{ route('relatorios.funcionarios.equipamentos', $funcionario) }}" target="_blank"
                        rel="noopener noreferrer"
                        class="px-4 py-2 rounded-lg border border-green-700 hover:bg-green-800/40 inline-flex items-center gap-2">
                        <i class="fa-solid fa-file-pdf"></i>
                        <span>Relatório de equipamentos</span>
                    </a>

                    {{-- Editar --}}
                    <a href="{{ route('funcionarios.edit', $funcionario->id) }}"
                        class="px-4 py-2 rounded-lg border border-green-700 hover:bg-green-800/40 inline-flex items-center gap-2"
                        title="Editar" aria-label="Editar">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span>Editar</span>
                    </a>

                    {{-- Excluir --}}
                    <form method="POST" action="{{ route('funcionarios.destroy', $funcionario->id) }}"
                        onsubmit="return confirm('Excluir o funcionário?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="cursor-pointer px-4 py-2 rounded-lg border border-red-700 text-red-200 hover:bg-red-900/30 inline-flex items-center gap-2"
                            aria-label="Excluir">
                            <i class="fa-solid fa-trash"></i>
                            <span>Excluir</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
