@extends('layouts.main_layout')

@section('content')
    <div class="w-full flex justify-center">
        <div class="w-full max-w-3xl bg-green-900/40 border border-green-800 rounded-2xl shadow-lg p-6 md:p-8 space-y-6">

            {{-- Cabeçalho --}}
            <header class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-wide">
                    Equipamento — #{{ $equipamento->id }}
                </h2>
                <p class="text-xs text-green-200">
                    Criado em: {{ $equipamento->criado_em?->format('d/m/Y H:i') ?? '-' }} ·
                    Atualizado em: {{ $equipamento->atualizado_em?->format('d/m/Y H:i') ?? '-' }}
                </p>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                <div class="md:col-span-3">
                    <label for="equipamento_id" class="mb-1 block text-sm text-green-100">ID</label>
                    <input id="equipamento_id" type="text" value="{{ $equipamento->id }}"
                        class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black
                           cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                        disabled readonly>
                </div>

                <div class="md:col-span-3">
                    <label for="equipamento_ativo" class="block mb-1 text-sm font-medium text-green-100">Ativo</label>
                    <div class="h-[42px] flex items-center gap-3">
                        <input id="equipamento_ativo" type="checkbox" disabled @checked($equipamento->ativo)
                            class="h-5 w-5 rounded border-green-700 bg-gray-300 text-green-700
                               cursor-default focus:outline-none focus:ring-0 focus:ring-offset-0">

                        @if ($equipamento->ativo)
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

            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                <div class="md:col-span-6">
                    <label for="tipo_nome" class="block mb-1 text-sm font-medium text-green-100">Tipo do Equipamento</label>
                    <input id="tipo_nome" type="text" value="{{ $equipamento->tipoEquipamento?->nome ?? '—' }}"
                        class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                        disabled readonly>
                </div>

                <div class="md:col-span-3">
                    <label for="patrimonio" class="block mb-1 text-sm font-medium text-green-100">Patrimônio</label>
                    <input id="patrimonio" type="text" value="{{ $equipamento->patrimonio ?? '—' }}"
                        class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                        disabled readonly>
                </div>

                <div class="md:col-span-3">
                    <label for="numero_serie" class="block mb-1 text-sm font-medium text-green-100">Número de Série</label>
                    <input id="numero_serie" type="text" value="{{ $equipamento->numero_serie ?? '—' }}"
                        class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                        disabled readonly>
                </div>
            </div>

            <fieldset class="rounded-xl border border-green-800 bg-green-900/60 p-4 md:p-5 space-y-4">
                <legend class="px-2 text-sm font-semibold tracking-wide text-green-200">
                    Aquisição
                </legend>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                    <div class="md:col-span-6">
                        <label for="data_compra" class="block mb-1 text-sm font-medium text-green-100">Data da
                            compra</label>
                        <input id="data_compra" type="text"
                            value="{{ $equipamento->data_compra?->format('d/m/Y') ?? '—' }}"
                            class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black
                          cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                            disabled readonly>
                    </div>

                    <div class="md:col-span-6">
                        <label for="valor_compra" class="block mb-1 text-sm font-medium text-green-100">Valor da
                            compra</label>
                        <input id="valor_compra" type="text"
                            value="{{ filled($equipamento->valor_compra) ? 'R$ ' . number_format((float) $equipamento->valor_compra, 2, ',', '.') : '—' }}"
                            class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black
                          cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                            disabled readonly>
                    </div>
                </div>
            </fieldset>


            <div>
                <label for="descricao" class="block mb-1 text-sm font-medium text-green-100">Descrição</label>
                <div id="descricao"
                    class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700 whitespace-pre-line min-h-[3.25rem]">
                    {{ $equipamento->descricao ?: '—' }}
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                {{-- Voltar --}}
                <a href="{{ route('equipamentos.index') }}"
                    class="px-4 py-2 rounded-lg border border-green-700 hover:bg-green-800/40 inline-flex items-center gap-2"
                    title="Voltar" aria-label="Voltar">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Voltar</span>
                </a>

                <div class="flex items-center gap-3">
                    <a href="{{ route('relatorios.equipamentos.historico', $equipamento) }}" target="_blank"
                        class="px-4 py-2 rounded-lg border border-green-700 hover:bg-green-800/40 inline-flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span>Histórico</span>
                    </a>

                    {{-- Editar --}}
                    <a href="{{ route('equipamentos.edit', $equipamento->id) }}"
                        class="px-4 py-2 rounded-lg border border-green-700 hover:bg-green-800/40 inline-flex items-center gap-2"
                        title="Editar" aria-label="Editar">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span>Editar</span>
                    </a>

                    {{-- Excluir --}}
                    <form method="POST" action="{{ route('equipamentos.destroy', $equipamento->id) }}"
                        onsubmit="return confirm('Excluir o equipamento? {{ addslashes($equipamento->nome) }}?');"
                        class="inline">
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
