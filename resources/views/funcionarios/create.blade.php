@extends('layouts.main_layout')

@section('content')
    <div class="w-full flex justify-center">
        <form id="funcionarioForm" action="{{ route('funcionarios.store') }}" method="POST"
            class="w-full max-w-3xl bg-green-900/40 border border-green-800 rounded-2xl shadow-lg p-6 md:p-8 space-y-6">
            @csrf

            {{-- Cabeçalho --}}
            <header class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-wide">Cadastro de Funcionário</h2>
                <p class="text-xs text-green-200">Escolha a empresa e o setor, e preencha as informações do funcionário.</p>
            </header>

            {{-- Resumo de erros --}}
            @if ($errors->any())
                <div class="rounded-lg border border-red-500/50 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                    <strong>Ops!</strong> Encontramos {{ $errors->count() }} campo(s) para revisar.
                </div>
            @endif

            {{-- Empresa --}}
            <div>
                <label class="mb-1 block text-sm text-green-100">Empresa</label>
                <select id="empresa_id" name="empresa_id" @class([
                    'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                    'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                        'empresa_id'),
                    'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                        'empresa_id'),
                ]) data-old="{{ old('empresa_id') }}"
                    data-url-base="{{ url('empresas') }}" required>
                    <option value="">Selecione...</option>
                    @foreach ($opcoesEmpresas as $id => $rotulo)
                        <option value="{{ $id }}" @selected(old('empresa_id') == $id)>
                            {{ $rotulo }}
                        </option>
                    @endforeach
                </select>
                @error('empresa_id')
                    <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm text-green-100">Setor</label>
                <select id="setor_id" name="setor_id" @class([
                    'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                    'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                        'setor_id'),
                    'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                        'setor_id'),
                ]) data-old="{{ old('setor_id') }}"
                    required>
                    <option value="">Selecione uma empresa primeiro...</option>
                </select>
                @error('setor_id')
                    <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Nome --}}
                <div>
                    <label class="mb-1 block text-sm text-green-100">Nome</label>
                    <input type="text" name="nome" value="{{ old('nome') }}" @class([
                        'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                        'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                            'nome'),
                        'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                            'nome'),
                    ]) required>
                    @error('nome')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Sobrenome --}}
                <div>
                    <label class="mb-1 block text-sm text-green-100">Sobrenome</label>
                    <input type="text" name="sobrenome" value="{{ old('sobrenome') }}" @class([
                        'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                        'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                            'sobrenome'),
                        'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                            'sobrenome'),
                    ])
                        required>
                    @error('sobrenome')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                {{-- CPF --}}
                <div>
                    <label class="mb-1 block text-sm text-green-100">CPF</label>
                    <input type="text" name="cpf" id="cpf" value="{{ old('cpf') }}"
                        @class([
                            'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                            'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                                'cpf'),
                            'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                                'cpf'),
                        ]) placeholder="000.000.000-00" required>
                    @error('cpf')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Matrícula --}}
                <div>
                    <label class="mb-1 block text-sm text-green-100">Matrícula</label>
                    <input type="text" id="matricula" name="matricula" value="{{ old('matricula') }}"
                        inputmode="numeric" pattern="\d*" autocomplete="off" @class([
                            'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 disabled:bg-gray-300 disabled:cursor-not-allowed placeholder-gray-500 focus:outline-none',
                            'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                                'matricula'),
                            'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                                'matricula'),
                        ])
                        placeholder="Somente números">
                    @error('matricula')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Telefone --}}
            <div>
                <label class="mb-1 block text-sm text-green-100">Telefone</label>
                <input type="text" id="telefone" name="telefone" value="{{ old('telefone') }}"
                    @class([
                        'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                        'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                            'telefone'),
                        'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                            'telefone'),
                    ]) placeholder="(44) 99999-0000">
                @error('telefone')
                    <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                @enderror
            </div>

            {{-- Flags --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="terceirizado" value="1" @checked(old('terceirizado', false))
                        class="h-5 w-5 rounded border border-green-700">
                    <span class="text-sm">Terceirizado</span>
                </label>
            </div>

            {{-- Ações --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ url()->previous() }}"
                    class="px-4 py-2 rounded-lg border border-green-700 hover:bg-green-800/40 transition">
                    <i class="fa-solid fa-arrow-left"></i>Cancelar
                </a>
                <button type="submit" class="px-5 py-2 rounded-lg bg-green-700 hover:bg-green-600 transition font-medium">
                    <i class="fa-solid fa-floppy-disk"></i> Salvar
                </button>
            </div>
    </div>
    </form>
    </div>

    @vite(['resources/js/masks.js', 'resources/js/funcionarios/funcionario-form.js'])
@endsection
