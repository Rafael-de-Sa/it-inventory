@extends('layouts.main_layout')

@section('content')
    <div class="w-full flex justify-center">
        <form id="setorEditForm" action="{{ route('setores.update', $setor->id) }}" method="POST"
            @class([
                'w-full max-w-3xl rounded-2xl shadow-lg p-6 md:p-8 space-y-6',
                'bg-green-900/40 border border-green-800',
            ])>
            @csrf
            @method('PUT')

            {{-- Cabeçalho --}}
            <header class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-wide">
                    Editar Setor — {{ $setor->nome }}
                </h2>
                <p class="text-xs text-green-200">
                    Criado em: {{ $setor->criado_em?->format('d/m/Y H:i') ?? '—' }} ·
                    Atualizado em: {{ $setor->atualizado_em?->format('d/m/Y H:i') ?? '—' }}
                </p>
            </header>

            {{-- Resumo de erros --}}
            @if ($errors->any())
                <div @class([
                    'rounded-lg border px-4 py-3 text-sm',
                    'border-red-500/50 bg-red-500/10 text-red-200',
                ])>
                    <strong>Ops!</strong> Encontramos {{ $errors->count() }} campo(s) para revisar.
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                {{-- ID (somente leitura) --}}
                <div class="md:col-span-3">
                    <label for="setor_id" class="block mb-1 text-sm font-medium text-green-100">ID</label>
                    <input id="setor_id" type="text" value="{{ $setor->id }}" @class([
                        'w-full rounded-lg border px-3 py-2 cursor-default',
                        'bg-gray-300 text-black border-green-700',
                    ]) disabled
                        readonly>
                </div>

                {{-- Ativo (checkbox real + hidden) --}}
                <div class="md:col-span-3">
                    <label for="ativo" class="block mb-1 text-sm font-medium text-green-100">Ativo</label>
                    <div class="h-[42px] flex items-center gap-3">
                        <input type="hidden" name="ativo" value="0">
                        <input id="ativo" name="ativo" type="checkbox" value="1" @checked(old('ativo', $setor->ativo))
                            @class([
                                'h-5 w-5 rounded focus:ring-0 focus:outline-none',
                                'border-green-700 text-green-700',
                            ])>

                        @if (old('ativo', $setor->ativo))
                            <span @class([
                                'inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium',
                                'border border-green-600/60 bg-green-600/15 text-green-100 ring-1 ring-inset ring-green-400/10',
                            ])>
                                <i class="fa-solid fa-check-circle text-[10px]"></i> Ativo
                            </span>
                        @else
                            <span @class([
                                'inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium',
                                'border border-gray-500/60 bg-gray-500/15 text-gray-200/80 ring-1 ring-inset ring-gray-400/10',
                            ])>
                                <i class="fa-solid fa-circle-xmark text-[10px]"></i> Inativo
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Nome do Setor --}}
            <div>
                <label for="nome" class="block mb-1 text-sm font-medium text-green-100">Nome do Setor*</label>
                <input id="nome" name="nome" type="text" maxlength="100" value="{{ old('nome', $setor->nome) }}"
                    @class([
                        'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                        'border-red-500 ring-1 ring-red-400' => $errors->has('nome'),
                        'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                            'nome'),
                    ])>
                @error('nome')
                    <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                @else
                    <p class="mt-1 text-xs text-green-200">Informe um nome claro, ex.: “Tecnologia da
                        Informação”.</p>
                @enderror
            </div>

            {{-- Empresa (combobox) --}}
            <div>
                <label for="empresa_id" class="block mb-1 text-sm font-medium text-green-100">Empresa*</label>
                <select id="empresa_id" name="empresa_id" @class([
                    'w-full rounded-lg border px-3 py-2 bg-white text-gray-900',
                    'border-green-700',
                ])>
                    <option value="">Selecione…</option>
                    @foreach ($opcoesEmpresas as $opt)
                        <option value="{{ $opt->id }}" @selected((string) old('empresa_id', $setor->empresa_id) === (string) $opt->id)>
                            {{ $opt->razao_social }} — {{ \App\Support\Mask::cnpj($opt->cnpj) }}
                        </option>
                    @endforeach
                </select>
                @error('empresa_id')
                    <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                @else
                    <p class="mt-1 text-xs text-green-200">Vincule o setor à empresa.</p>
                @enderror
            </div>

            {{-- Ações --}}
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('setores.show', $setor->id) }}" @class([
                    'px-4 py-2 rounded-lg border inline-flex items-center gap-2',
                    'border-green-700 hover:bg-green-800/40',
                ]) title="Cancelar"
                    aria-label="Cancelar">
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
