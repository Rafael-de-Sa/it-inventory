@extends('layouts.main_layout')

@section('content')
    <div class="w-full flex justify-center">
        <form id="equipamentoForm" action="{{ route('equipamentos.store') }}" method="POST"
            class="w-full max-w-3xl bg-green-900/40 border border-green-800 rounded-2xl shadow-lg p-6 md:p-8 space-y-6">
            @csrf

            {{-- Cabeçalho --}}
            <header class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-wide">Cadastro de Equipamento</h2>
                <p class="text-xs text-green-200">Preencha os dados do equipamento.</p>
            </header>

            {{-- Resumo de erros --}}
            @if ($errors->any())
                <div class="rounded-lg border border-red-500/50 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                    <strong>Ops!</strong> Encontramos {{ $errors->count() }} campo(s) para revisar.
                </div>
            @endif

            {{-- Linha 1: Tipo de equipamento + Status --}}
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                {{-- Tipo de equipamento --}}
                <div class="md:col-span-7">
                    <label for="tipo_equipamento_id" class="mb-1 block text-sm text-green-100">Tipo de Equipamento</label>
                    <select id="tipo_equipamento_id" name="tipo_equipamento_id" @class([
                        'w-full rounded-lg border px-3 py-2 bg-white text-gray-900',
                        'border-green-700' => !$errors->has('tipo_equipamento_id'),
                        'border-red-500' => $errors->has('tipo_equipamento_id'),
                    ])>
                        <option value="">Selecione...</option>
                        @foreach ($opcoesTipos as $id => $nomeTipo)
                            <option value="{{ $id }}" @selected(old('tipo_equipamento_id') == $id)>{{ $nomeTipo }}</option>
                        @endforeach
                    </select>
                    @error('tipo_equipamento_id')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="md:col-span-5">
                    <label for="status" class="mb-1 block text-sm text-green-100">Status</label>
                    <select id="status" name="status" @class([
                        'w-full rounded-lg border px-3 py-2 bg-white text-gray-900',
                        'border-green-700' => !$errors->has('status'),
                        'border-red-500' => $errors->has('status'),
                    ])>
                        @foreach ($listaStatus as $valor => $rotulo)
                            <option value="{{ $valor }}" @selected(old('status', 'disponivel') === $valor)>
                                {{ $rotulo }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Linha 2: Patrimônio + Número de Série --}}
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                {{-- Patrimônio --}}
                <div class="md:col-span-6">
                    <label for="patrimonio" class="mb-1 block text-sm text-green-100">Patrimônio</label>
                    <input id="patrimonio" name="patrimonio" type="text" maxlength="50" value="{{ old('patrimonio') }}"
                        @class([
                            'w-full rounded-lg border px-3 py-2 bg-white text-gray-900',
                            'border-green-700' => !$errors->has('patrimonio'),
                            'border-red-500' => $errors->has('patrimonio'),
                        ]) />
                    @error('patrimonio')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Número de Série --}}
                <div class="md:col-span-6">
                    <label for="numero_serie" class="mb-1 block text-sm text-green-100">Número de Série</label>
                    <input id="numero_serie" name="numero_serie" type="text" maxlength="100"
                        value="{{ old('numero_serie') }}" @class([
                            'w-full rounded-lg border px-3 py-2 bg-white text-gray-900',
                            'border-green-700' => !$errors->has('numero_serie'),
                            'border-red-500' => $errors->has('numero_serie'),
                        ]) />
                    @error('numero_serie')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Linha 3: Data da compra + Valor da compra --}}
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                {{-- Data da compra --}}
                <div class="md:col-span-6">
                    <label for="data_compra" class="mb-1 block text-sm text-green-100">Data da Compra</label>
                    <input id="data_compra" name="data_compra" type="date" value="{{ old('data_compra') }}"
                        @class([
                            'w-full rounded-lg border px-3 py-2 bg-white text-gray-900',
                            'border-green-700' => !$errors->has('data_compra'),
                            'border-red-500' => $errors->has('data_compra'),
                        ]) />
                    @error('data_compra')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Valor da compra --}}
                <div class="md:col-span-6">
                    <label for="valor_compra" class="mb-1 block text-sm text-green-100">Valor da Compra</label>
                    <input id="valor_compra" name="valor_compra" type="text" inputmode="decimal"
                        placeholder="Ex.: 1.234,56" value="{{ old('valor_compra') }}" @class([
                            'w-full rounded-lg border px-3 py-2 bg-white text-gray-900',
                            'border-green-700' => !$errors->has('valor_compra'),
                            'border-red-500' => $errors->has('valor_compra'),
                        ]) />
                    @error('valor_compra')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Linha 4: Descrição --}}
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                {{-- Descrição --}}
                <div class="md:col-span-12">
                    <label for="descricao" class="mb-1 block text-sm text-green-100">Descrição</label>
                    <textarea id="descricao" name="descricao" rows="4" @class([
                        'w-full rounded-lg border px-3 py-2 bg-white text-gray-900',
                        'border-green-700' => !$errors->has('descricao'),
                        'border-red-500' => $errors->has('descricao'),
                    ])
                        placeholder="Observações do equipamento (opcional)">{{ old('descricao') }}</textarea>
                    @error('descricao')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>
            </div>


            {{-- Rodapé: Ações --}}
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('equipamentos.index') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-green-700 bg-transparent px-4 py-2 text-sm hover:bg-green-700/20">
                    <i class="fa-solid fa-arrow-left"></i> Cancelar
                </a>

                <button type="submit" class="px-5 py-2 rounded-lg bg-green-700 hover:bg-green-600 transition font-medium">
                    <i class="fa-solid fa-floppy-disk"></i> Salvar
                </button>
            </div>
        </form>
    </div>
@endsection
