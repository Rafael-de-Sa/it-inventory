@extends('layouts.main_layout')

@section('content')
    <div class="w-full flex justify-center">
        <form id="equipamentoEditForm" action="{{ route('equipamentos.update', $equipamento->id) }}" method="POST"
            class="w-full max-w-3xl bg-green-900/40 border border-green-800 rounded-2xl shadow-lg p-6 md:p-8 space-y-6">
            @csrf
            @method('PUT')

            {{-- Cabeçalho --}}
            <header class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-wide">
                    Editar Equipamento — #{{ $equipamento->id }}
                </h2>
                <p class="text-xs text-green-200">
                    Criado em: {{ $equipamento->criado_em?->format('d/m/Y H:i') ?? '—' }} ·
                    Atualizado em: {{ $equipamento->atualizado_em?->format('d/m/Y H:i') ?? '—' }}
                </p>
            </header>

            {{-- Resumo de erros --}}
            @if ($errors->any())
                <div class="rounded-lg border border-red-500/50 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                    <strong>Ops!</strong> Encontramos {{ $errors->count() }} campo(s) para revisar.
                </div>
            @endif

            {{-- Linha 1: ID (readonly) + Status --}}
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                {{-- ID --}}
                <div class="md:col-span-3">
                    <label for="equipamento_id" class="mb-1 block text-sm text-green-100">ID</label>
                    <input id="equipamento_id" type="text" value="{{ $equipamento->id }}"
                        class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black
                              cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                        disabled readonly>
                </div>

                {{-- Status --}}
                <div class="md:col-span-4">
                    <label for="status" class="mb-1 block text-sm text-green-100">Status*</label>
                    @php $statusAtual = old('status', $equipamento->status); @endphp
                    <select id="status" name="status" @class([
                        'w-full rounded-lg border px-3 py-2 bg-white text-gray-900',
                        'border-green-700' => !$errors->has('status'),
                        'border-red-500' => $errors->has('status'),
                    ])>
                        <option value="disponivel" @selected($statusAtual === 'disponivel')>Disponível</option>
                        <option value="em_uso" @selected($statusAtual === 'em_uso')>Em uso</option>
                        <option value="em_manutencao" @selected($statusAtual === 'em_manutencao')>Em manutenção</option>
                        <option value="defeituoso" @selected($statusAtual === 'defeituoso')>Defeituoso</option>
                        <option value="descartado" @selected($statusAtual === 'descartado')>Descartado</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Linha 2: Tipo / Patrimônio / Número de Série --}}
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                {{-- Tipo do Equipamento --}}
                <div class="md:col-span-6">
                    <label for="tipo_equipamento_id" class="block mb-1 text-sm font-medium text-green-100">Tipo do
                        Equipamento*</label>
                    <select id="tipo_equipamento_id" name="tipo_equipamento_id" @class([
                        'w-full rounded-lg border px-3 py-2 bg-white text-gray-900',
                        'border-green-700' => !$errors->has('tipo_equipamento_id'),
                        'border-red-500' => $errors->has('tipo_equipamento_id'),
                    ])>
                        @foreach ($opcoesTiposEquipamento as $id => $nome)
                            <option value="{{ $id }}" @selected(old('tipo_equipamento_id', $equipamento->tipo_equipamento_id) == $id)>
                                {{ $nome }}
                            </option>
                        @endforeach
                    </select>
                    @error('tipo_equipamento_id')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Patrimônio --}}
                <div class="md:col-span-3">
                    <label for="patrimonio" class="block mb-1 text-sm font-medium text-green-100">Patrimônio</label>
                    <input id="patrimonio" name="patrimonio" type="text"
                        value="{{ old('patrimonio', $equipamento->patrimonio) }}" @class([
                            'w-full rounded-lg border px-3 py-2 bg-white text-gray-900',
                            'border-green-700' => !$errors->has('patrimonio'),
                            'border-red-500' => $errors->has('patrimonio'),
                        ])>
                    @error('patrimonio')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Número de Série (será upper no mutator/update) --}}
                <div class="md:col-span-3">
                    <label for="numero_serie" class="block mb-1 text-sm font-medium text-green-100">Número de Série</label>
                    <input id="numero_serie" name="numero_serie" type="text"
                        value="{{ old('numero_serie', $equipamento->numero_serie) }}" @class([
                            'w-full rounded-lg border px-3 py-2 bg-white text-gray-900',
                            'border-green-700' => !$errors->has('numero_serie'),
                            'border-red-500' => $errors->has('numero_serie'),
                        ])>
                    @error('numero_serie')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Aquisição --}}
            <fieldset class="rounded-xl border border-green-800 bg-green-900/60 p-4 md:p-5 space-y-4">
                <legend class="px-2 text-sm font-semibold tracking-wide text-green-200 uppercase">Aquisição</legend>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                    {{-- Data da compra --}}
                    <div class="md:col-span-6">
                        <label for="data_compra" class="block mb-1 text-sm font-medium text-green-100">Data da
                            compra</label>
                        <input id="data_compra" name="data_compra" type="date"
                            value="{{ old('data_compra', optional($equipamento->data_compra)->format('Y-m-d')) }}"
                            @class([
                                'w-full rounded-lg border px-3 py-2 bg-white text-gray-900',
                                'border-green-700' => !$errors->has('data_compra'),
                                'border-red-500' => $errors->has('data_compra'),
                            ])>
                        @error('data_compra')
                            <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Valor da compra --}}
                    <div class="md:col-span-6">
                        <label for="valor_compra" class="block mb-1 text-sm font-medium text-green-100">Valor da
                            compra</label>
                        <input id="valor_compra" name="valor_compra" type="number" step="0.01" inputmode="decimal"
                            value="{{ old('valor_compra', $equipamento->valor_compra) }}" @class([
                                'w-full rounded-lg border px-3 py-2 bg-white text-gray-900',
                                'border-green-700' => !$errors->has('valor_compra'),
                                'border-red-500' => $errors->has('valor_compra'),
                            ])>
                        @error('valor_compra')
                            <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </fieldset>

            {{-- Descrição --}}
            <div>
                <label for="descricao" class="block mb-1 text-sm font-medium text-green-100">Descrição*</label>
                <textarea id="descricao" name="descricao" rows="4" @class([
                    'w-full rounded-lg border px-3 py-2 bg-white text-gray-900',
                    'border-green-700' => !$errors->has('descricao'),
                    'border-red-500' => $errors->has('descricao'),
                ])
                    placeholder="Observações (opcional)">{{ old('descricao', $equipamento->descricao) }}</textarea>
                @error('descricao')
                    <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                @enderror
            </div>

            {{-- Barra de ações --}}
            <div class="flex items-center justify-between pt-2">
                <a href="{{ url()->previous() }}" @class([
                    'px-4 py-2 rounded-lg border inline-flex items-center gap-2',
                    'border-green-700 hover:bg-green-800/40',
                ]) title="Cancelar" aria-label="Cancelar">
                    <i class="fa-solid fa-arrow-left"></i><span>Cancelar</span>
                </a>

                <button type="submit" @class([
                    'px-5 py-2 rounded-lg font-medium inline-flex items-center gap-2',
                    'bg-green-700 hover:bg-green-600 text-white',
                ])>
                    <i class="fa-solid fa-floppy-disk"></i><span>Salvar</span>
                </button>
            </div>
        </form>
    </div>
@endsection
