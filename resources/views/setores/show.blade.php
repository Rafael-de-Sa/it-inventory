@extends('layouts.main_layout')

@section('content')
    <div class="w-full flex justify-center">
        <div class="w-full max-w-3xl bg-green-900/40 border border-green-800 rounded-2xl shadow-lg p-6 md:p-8 space-y-6">

            <header class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-wide">
                    Setor — {{ $setor->nome }}
                </h2>
                <p class="text-xs text-green-200">
                    Criado em: {{ $setor->criado_em?->format('d/m/Y H:i') ?? $setor->created_at?->format('d/m/Y H:i') }} ·
                    Atualizado em:
                    {{ $setor->atualizado_em?->format('d/m/Y H:i') ?? $setor->updated_at?->format('d/m/Y H:i') }}
                </p>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                <div class="md:col-span-3">
                    <label for="setor_id" class="mb-1 block text-sm text-green-100">ID</label>
                    <input id="setor_id" type="text" value="{{ $setor->id }}"
                        class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black
                               cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                        disabled readonly>
                </div>

                <div class="md:col-span-3">
                    <label for="setor_ativo" class="block mb-1 text-sm font-medium text-green-100">Ativo</label>
                    <div class="h-[42px] flex items-center gap-3">
                        <input id="setor_ativo" type="checkbox" disabled @checked($setor->ativo)
                            class="h-5 w-5 rounded border-green-700 bg-gray-300 text-green-700
                                   cursor-default focus:outline-none focus:ring-0 focus:ring-offset-0">

                        @if ($setor->ativo)
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

            <div>
                <label for="nome" class="block mb-1 text-sm font-medium text-green-100">Nome do Setor</label>
                <input id="nome" type="text" value="{{ $setor->nome }}"
                    class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                    disabled readonly>
            </div>

            <fieldset class="rounded-xl border border-green-800 bg-green-900/60 p-4 md:p-5 space-y-4">
                <legend class="px-2 text-sm font-semibold tracking-wide text-green-200">Empresa</legend>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                    <div class="md:col-span-8">
                        <label for="empresa_nome" class="block mb-1 text-sm font-medium text-green-100">Nome da
                            Empresa</label>
                        <input id="empresa_nome" type="text" value="{{ $setor->empresa?->razao_social ?? '—' }}"
                            class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                            disabled readonly>
                    </div>

                    <div class="md:col-span-4">
                        <label for="empresa_cnpj" class="block mb-1 text-sm font-medium text-green-100">CNPJ</label>
                        <input id="empresa_cnpj" type="text"
                            value="{{ \App\Support\Mask::cnpj($setor->empresa?->cnpj) }}"
                            class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                            disabled readonly>
                    </div>
                </div>

            </fieldset>

            {{-- Ações --}}
            <div class="flex items-center justify-between pt-2">
                {{-- Voltar --}}
                <a href="{{ route('setores.index') }}"
                    class="px-4 py-2 rounded-lg border border-green-700 hover:bg-green-800/40 inline-flex items-center gap-2"
                    title="Voltar" aria-label="Voltar">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Voltar</span>
                </a>

                <div class="flex items-center gap-3">
                    {{-- Editar --}}
                    <a href="{{ route('setores.edit', $setor->id) }}"
                        class="px-4 py-2 rounded-lg border border-green-700 hover:bg-green-800/40 inline-flex items-center gap-2"
                        title="Editar" aria-label="Editar">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span>Editar</span>
                    </a>

                    {{-- Excluir --}}
                    <form method="POST" action="{{ route('setores.destroy', $setor->id) }}"
                        onsubmit="return confirm('Excluir o setor {{ addslashes($setor->nome) }}?');" class="inline">
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
