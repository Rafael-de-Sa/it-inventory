@extends('layouts.main_layout')

@section('content')
    <div class="w-full flex justify-center">
        <form id="tipoEquipamentoForm" action="{{ route('tipo-equipamentos.store') }}" method="POST"
            class="w-full max-w-3xl bg-green-900/40 border border-green-800 rounded-2xl shadow-lg p-6 md:p-8 space-y-6">
            @csrf

            {{-- Cabeçalho --}}
            <header class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-wide">Cadastro de Tipo de Equipamento</h2>
                <p class="text-xs text-green-200">Informe o nome do tipo.</p>
            </header>

            {{-- Resumo de erros --}}
            @if ($errors->any())
                <div class="rounded-lg border border-red-500/50 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                    <strong>Ops!</strong> Encontramos {{ $errors->count() }} campo(s) para revisar.
                </div>
            @endif

            {{-- Campo: Nome --}}
            <div class="space-y-1">
                <label for="nome" class="block text-sm text-green-100">Nome*</label>
                <input type="text" id="nome" name="nome" value="{{ old('nome') }}"
                    placeholder="Ex.: Notebook, Desktop, Impressora..." @class([
                        'w-full rounded-lg bg-white px-3 py-2 text-gray-900 border focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-400',
                        'border-green-700' => !$errors->has('nome'),
                        'border-red-500' => $errors->has('nome'),
                    ]) autocomplete="off"
                    autofocus required>
                @error('nome')
                    <p class="text-xs text-red-300">{{ $message }}</p>
                @else
                    <p class="text-xs text-green-200">Informe um nome claro, ex.: “Notebook”.</p>
                @enderror
            </div>

            {{-- Ações (padrão Setor: Cancelar outline + Salvar sólido) --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('tipo-equipamentos.index') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-green-700 px-4 py-2 hover:bg-green-800/40"
                    title="Cancelar">
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
