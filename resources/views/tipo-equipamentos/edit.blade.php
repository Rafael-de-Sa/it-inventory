@extends('layouts.main_layout')

@section('content')
    <div class="w-full flex justify-center">
        <form id="tipoEquipamentoEditForm"
              action="{{ route('tipo-equipamentos.update', $tipoEquipamento->id) }}"
              method="POST"
              class="w-full max-w-3xl bg-green-900/40 border border-green-800 rounded-2xl shadow-lg p-6 md:p-8 space-y-6">
            @csrf
            @method('PUT')

            {{-- Cabeçalho --}}
            <header class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-wide">
                    Editar Tipo de Equipamento — {{ $tipoEquipamento->nome }}
                </h2>
                <p class="text-xs text-green-200">
                    Criado em: {{ ($tipoEquipamento->criado_em ?? $tipoEquipamento->created_at)?->format('d/m/Y H:i') }}
                    · Atualizado em: {{ ($tipoEquipamento->atualizado_em ?? $tipoEquipamento->updated_at)?->format('d/m/Y H:i') }}
                </p>
            </header>

            {{-- Resumo de erros --}}
            @if ($errors->any())
                <div class="rounded-lg border border-red-500/50 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                    <strong>Ops!</strong> Encontramos {{ $errors->count() }} campo(s) para revisar.
                </div>
            @endif

            {{-- Linha: ID + Ativo --}}
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                {{-- ID (readonly) --}}
                <div class="md:col-span-3">
                    <label class="block mb-1 text-sm font-medium text-green-100">ID</label>
                    <input type="text" value="{{ $tipoEquipamento->id }}"  @class([
                        'w-full rounded-lg border px-3 py-2 cursor-default',
                        'bg-gray-300 text-black border-green-700',
                    ]) disabled
                        readonly>
                </div>

                {{-- Ativo (checkbox) --}}
                <div class="md:col-span-3">
                    <label for="ativo" class="block mb-1 text-sm font-medium text-green-100">Ativo</label>
                    <div class="h-[42px] flex items-center gap-3">
                        <input type="hidden" name="ativo" value="0">
                        <input id="ativo" name="ativo" type="checkbox" value="1" @checked(old('ativo', $tipoEquipamento->ativo))
                            @class([
                                'h-5 w-5 rounded focus:ring-0 focus:outline-none',
                                'border-green-700 text-green-700',
                            ])>

                        @if (old('ativo', $tipoEquipamento->ativo))
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

            {{-- Nome --}}
            <div class="space-y-1">
                <label for="nome" class="block text-sm text-green-100">Nome*</label>
                <input
                    type="text"
                    id="nome"
                    name="nome"
                    value="{{ old('nome', $tipoEquipamento->nome) }}"
                    placeholder="Ex.: Notebook, Desktop, Impressora..."
                    @class([
                        'w-full rounded-lg bg-white px-3 py-2 text-gray-900 border focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-400',
                        'border-green-700' => !$errors->has('nome'),
                        'border-red-500' => $errors->has('nome'),
                    ])
                    autocomplete="off"
                    required
                >
                @error('nome')
                    <p class="text-xs text-red-300">{{ $message }}</p>
                @else
                    <p class="text-xs text-green-200">Informe um nome claro, ex.: “Notebook”.</p>
                @enderror
            </div>

            {{-- Ações (Cancelar outline + Salvar sólido) --}}
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('tipo-equipamentos.show', $tipoEquipamento->id) }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-green-700 px-4 py-2 hover:bg-green-800/40">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Cancelar</span>
                </a>

                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-green-700 px-4 py-2 text-green-50 hover:bg-green-600">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Salvar</span>
                </button>
            </div>
        </form>
    </div>
@endsection
