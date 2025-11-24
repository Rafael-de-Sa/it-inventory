@extends('layouts.main_layout')

@section('content')
    <div class="w-full flex justify-center">
        <form id="setorForm" action="{{ route('setores.store') }}" method="POST"
            class="w-full max-w-3xl bg-green-900/40 border border-green-800 rounded-2xl shadow-lg p-6 md:p-8 space-y-6">
            @csrf

            <header class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-wide">Cadastro de Setor</h2>
                <p class="text-xs text-green-200">Vincule o setor a uma empresa e informe o nome.</p>
            </header>

            @if ($errors->any())
                <div class="rounded-lg border border-red-500/50 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                    <strong>Ops!</strong> Encontramos {{ $errors->count() }} campo(s) para revisar.
                </div>
            @endif

            <div>
                <label for="empresa_id" class="block mb-1 text-sm font-medium text-green-100">Empresa*</label>
                <select id="empresa_id" name="empresa_id" required
                    class="w-full rounded-lg border px-3 py-2 bg-white text-gray-900">
                    <option value="" disabled {{ old('empresa_id') ? '' : 'selected' }}>Selecione...</option>
                    @foreach ($empresas as $empresa)
                        <option value="{{ $empresa->id }}" @selected(old('empresa_id') == $empresa->id)>
                            {{ $empresa->id }} - {{ \App\Support\Mask::cnpj($empresa->cnpj) }} -
                            {{ $empresa->razao_social }}
                        </option>
                    @endforeach
                </select>
                @if ($errors->has('empresa_id'))
                    <p id="empresa_id_help" class="mt-1 text-xs text-red-300 flex items-center gap-1">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                        </svg>
                        {{ $errors->first('empresa_id') }}
                    </p>
                @else
                    <p id="empresa_id_help" class="mt-1 text-xs text-green-200">Escolha a empresa do setor.</p>
                @endif
            </div>

            <div>
                <label for="nome" class="block mb-1 text-sm font-medium text-green-100">Nome do Setor*</label>
                <input id="nome" name="nome" type="text" maxlength="50"
                    placeholder="Ex.: Financeiro ou Departamento Pessoal" value="{{ old('nome') }}"
                    @class([
                        'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                        'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                            'nome'),
                        'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                            'nome'),
                    ]) aria-invalid="{{ $errors->has('nome') ? 'true' : 'false' }}"
                    aria-describedby="nome_help">
                @if ($errors->has('nome'))
                    <p id="nome_help" class="mt-1 text-xs text-red-300 flex items-center gap-1">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                        </svg>
                        {{ $errors->first('nome') }}
                    </p>
                @else
                    <p id="nome_help" class="mt-1 text-xs text-green-200">Informe um nome claro, ex.: “Tecnologia da
                        Informação”.</p>
                @endif
            </div>

            {{-- Ações --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('setores.index') }}"
                    class="px-4 py-2 rounded-lg border border-green-700 hover:bg-green-800/40 transition">
                    <i class="fa-solid fa-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="px-5 py-2 rounded-lg bg-green-700 hover:bg-green-600 transition font-medium">
                    <i class="fa-solid fa-floppy-disk"></i> Salvar
                </button>
            </div>
        </form>
    </div>
@endsection
