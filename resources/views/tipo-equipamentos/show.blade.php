@extends('layouts.main_layout')

@section('content')
    <div class="w-full flex justify-center">
        <div class="w-full max-w-3xl bg-green-900/40 border border-green-800 rounded-2xl shadow-lg p-6 md:p-8 space-y-6">

            {{-- Cabeçalho (mesmo padrão) --}}
            <header class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-wide">
                    Tipo de Equipamento — {{ $tipoEquipamento->nome }}
                </h2>
                <p class="text-xs text-green-200">
                    Criado em: {{ ($tipoEquipamento->criado_em ?? $tipoEquipamento->created_at)?->format('d/m/Y H:i') }}
                    · Atualizado em: {{ ($tipoEquipamento->atualizado_em ?? $tipoEquipamento->updated_at)?->format('d/m/Y H:i') }}
                </p>
            </header>

            {{-- Linha 1: ID + Ativo (idêntico ao empresas.show) --}}
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                {{-- ID readonly --}}
                <div class="md:col-span-3">
                    <label class="mb-1 block text-sm text-green-100">ID</label>
                    <input
                        type="text"
                        value="{{ $tipoEquipamento->id }}"
                        class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black
                               cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                        disabled readonly
                    >
                </div>

                {{-- Ativo: checkbox desabilitado + chip (alinhado na linha, tom e hover iguais ao empresas.show) --}}
                 <div class="md:col-span-3">
                    <label for="setor_ativo" class="block mb-1 text-sm font-medium text-green-100">Ativo</label>
                    <div class="h-[42px] flex items-center gap-3">
                        <input id="setor_ativo" type="checkbox" disabled @checked($tipoEquipamento->ativo)
                            class="h-5 w-5 rounded border-green-700 bg-gray-300 text-green-700
                                   cursor-default focus:outline-none focus:ring-0 focus:ring-offset-0">

                        @if ($tipoEquipamento->ativo)
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
            </div>

            {{-- Nome readonly (mesmo look dos inputs do empresas.show) --}}
            <div>
                <label class="mb-1 block text-sm text-green-100">Nome</label>
                <input
                    type="text"
                    value="{{ $tipoEquipamento->nome }}"
                    class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                    disabled readonly
                >
            </div>

            {{-- Rodapé de ações (Voltar à esquerda | Editar + Excluir à direita) --}}
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('tipo-equipamentos.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-green-700 px-4 py-2 hover:bg-green-800/40">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Voltar</span>
                </a>

                <div class="flex items-center gap-2">
                    <a href="{{ route('tipo-equipamentos.edit', $tipoEquipamento->id) }}"
                       class="inline-flex items-center gap-2 rounded-lg border border-green-700 px-4 py-2 hover:bg-green-800/40">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span>Editar</span>
                    </a>

                    <form action="{{ route('tipo-equipamentos.destroy', $tipoEquipamento->id) }}"
                          method="POST"
                          onsubmit="return confirm('Tem certeza que deseja remover este tipo?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-lg border border-red-700 px-4 py-2 text-red-200 hover:bg-red-800/20 focus:outline-none focus:ring-2 focus:ring-red-500">
                            <i class="fa-solid fa-trash-can"></i>
                            <span>Excluir</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
