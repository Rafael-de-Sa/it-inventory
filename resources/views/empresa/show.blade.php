@extends('layouts.main_layout')

@section('content')
    <div class="w-full flex justify-center">
        <div class="w-full max-w-3xl bg-green-900/40 border border-green-800 rounded-2xl shadow-lg p-6 md:p-8 space-y-6">

            {{-- Cabeçalho --}}
            <header class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-wide">
                    Empresa — {{ $empresa->razao_social }}
                </h2>
                <p class="text-xs text-green-200">
                    Criada em: {{ $empresa->criado_em?->format('d/m/Y H:i') }} ·
                    Atualizada em: {{ $empresa->atualizado_em?->format('d/m/Y H:i') }}
                </p>
            </header>

            {{-- ID + Ativa (checkbox alinhado ao input) --}}
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                {{-- ID (tamanho CEP) --}}
                <div class="md:col-span-3">
                    <label for="empresa_id" class="block mb-1 text-sm font-medium text-green-100">ID</label>
                    <input id="empresa_id" type="text" value="{{ $empresa->id }}"
                        class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black
                      cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                        disabled readonly>
                </div>

                {{-- Ativa (checkbox) --}}
                <div class="md:col-span-3">
                    <label for="empresa_ativa" class="block mb-1 text-sm font-medium text-green-100">Ativa</label>

                    {{-- mesma “altura de campo” do input ao lado (≈ 42px) --}}
                    <div class="h-[42px] flex items-center gap-3">
                        <input id="empresa_ativa" type="checkbox" disabled @checked($empresa->ativo)
                            class="h-5 w-5 rounded border-green-700 bg-gray-300 text-green-700
                          cursor-default focus:outline-none focus:ring-0 focus:ring-offset-0">

                        @if ($empresa->ativo)
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium
                                       border border-green-600/60 bg-green-600/15 text-green-100
                                       ring-1 ring-inset ring-green-400/10 shadow-sm">
                                <i class="fa-solid fa-check-circle text-[10px]"></i>
                                Ativa
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium
                                       border border-gray-500/60 bg-gray-500/15 text-gray-200/80
                                       ring-1 ring-inset ring-gray-400/10 shadow-sm">
                                <i class="fa-solid fa-circle-xmark text-[10px]"></i>
                                Inativa
                            </span>
                        @endif

                    </div>
                </div>
            </div>



            {{-- Nome Fantasia --}}
            <div>
                <label for="nome_fantasia" class="block mb-1 text-sm font-medium text-green-100">Nome Fantasia</label>
                <input id="nome_fantasia" type="text" value="{{ $empresa->nome_fantasia }}"
                    class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                    disabled readonly>
            </div>

            {{-- Razão Social --}}
            <div>
                <label for="razao_social" class="block mb-1 text-sm font-medium text-green-100">Razão Social</label>
                <input id="razao_social" type="text" value="{{ $empresa->razao_social }}"
                    class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                    disabled readonly>
            </div>

            {{-- CNPJ --}}
            <div>
                <label for="cnpj" class="block mb-1 text-sm font-medium text-green-100">CNPJ</label>
                <input id="cnpj" type="text" value="{{ $empresa->cnpj_masked ?? $empresa->cnpj }}"
                    class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                    disabled readonly>
            </div>

            {{-- Endereço --}}
            <fieldset class="rounded-xl border border-green-800 bg-green-900/60 p-4 md:p-5 space-y-4">
                <legend class="px-2 text-sm font-semibold tracking-wide text-green-200 uppercase">Endereço</legend>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                    {{-- CEP --}}
                    <div class="md:col-span-3">
                        <label for="cep" class="block mb-1 text-sm font-medium text-green-100">CEP</label>
                        <input id="cep" type="text" value="{{ $empresa->cep_masked ?? $empresa->cep }}"
                            class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                            disabled readonly>
                    </div>

                    {{-- Rua --}}
                    <div class="md:col-span-9">
                        <label for="rua" class="block mb-1 text-sm font-medium text-green-100">Rua</label>
                        <input id="rua" type="text" value="{{ $empresa->rua }}"
                            class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                            disabled readonly>
                    </div>

                    {{-- Número --}}
                    <div class="md:col-span-3">
                        <label for="numero" class="block mb-1 text-sm font-medium text-green-100">Número</label>
                        <input id="numero" type="text" value="{{ $empresa->numero }}"
                            class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                            disabled readonly>
                    </div>

                    {{-- Bairro --}}
                    <div class="md:col-span-4">
                        <label for="bairro" class="block mb-1 text-sm font-medium text-green-100">Bairro</label>
                        <input id="bairro" type="text" value="{{ $empresa->bairro }}"
                            class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                            disabled readonly>
                    </div>

                    {{-- Complemento --}}
                    <div class="md:col-span-5">
                        <label for="complemento" class="block mb-1 text-sm font-medium text-green-100">Complemento</label>
                        <input id="complemento" type="text" value="{{ $empresa->complemento }}"
                            class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                            disabled readonly>
                    </div>

                    {{-- Cidade --}}
                    <div class="md:col-span-8">
                        <label for="cidade" class="block mb-1 text-sm font-medium text-green-100">Cidade</label>
                        <input id="cidade" type="text" value="{{ $empresa->cidade }}"
                            class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                            disabled readonly>
                    </div>

                    {{-- Estado (UF) --}}
                    <div class="md:col-span-4">
                        <label for="estado" class="block mb-1 text-sm font-medium text-green-100">Estado</label>
                        <input id="estado" type="text" value="{{ $empresa->estado }}"
                            class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black uppercase cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                            disabled readonly>
                    </div>
                </div>
            </fieldset>

            {{-- E-mail --}}
            <div>
                <label for="email" class="block mb-1 text-sm font-medium text-green-100">E-mail</label>
                <input id="email" type="text" value="{{ $empresa->email }}"
                    class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                    disabled readonly>
            </div>

            {{-- Telefone --}}
            <div>
                <label for="telefone" class="block mb-1 text-sm font-medium text-green-100">Telefone</label>
                <input id="telefone" type="text" value="{{ $empresa->telefone_masked ?? $empresa->telefone }}"
                    class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                    disabled readonly>
            </div>

            {{-- Ações --}}
            <div class="flex items-center justify-between pt-2">
                {{-- Voltar para a lista --}}
                <a href="{{ route('empresa.index') }}"
                    class="px-4 py-2 rounded-lg border border-green-700 hover:bg-green-800/40 inline-flex items-center gap-2"
                    title="Voltar" aria-label="Voltar">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Voltar</span>
                </a>

                <div class="flex items-center gap-3">
                    {{-- Editar --}}
                    <a href="{{ route('empresa.edit', $empresa->id) }}"
                        class="px-4 py-2 rounded-lg border border-green-700 hover:bg-green-800/40 inline-flex items-center gap-2"
                        title="Editar" aria-label="Editar">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span>Editar</span>
                    </a>

                    {{-- Excluir --}}
                    <form method="POST" action="{{ route('empresa.destroy', $empresa->id) }}"
                        onsubmit="return confirm('Excluir a empresa {{ addslashes($empresa->razao_social) }}?');"
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
