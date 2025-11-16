@extends('layouts.main_layout')

@section('content')
    <div class="w-full flex justify-center">
        <form id="funcionarioEditForm" action="{{ route('funcionarios.update', $funcionario->id) }}" method="POST"
            class="w-full max-w-3xl bg-green-900/40 border border-green-800 rounded-2xl shadow-lg p-6 md:p-8 space-y-6">
            @csrf
            @method('PUT')

            {{-- Cabeçalho --}}
            <header class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-wide">Editar Funcionário — {{ $funcionario->nome }}</h2>
                <p class="text-xs text-green-200">Atualize as informações do funcionário.</p>
            </header>

            {{-- Resumo de erros --}}
            @if ($errors->any())
                <div class="rounded-lg border border-red-500/50 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                    <strong>Ops!</strong> Encontramos {{ $errors->count() }} campo(s) para revisar.
                </div>
            @endif

            {{-- Empresa (apenas para filtrar setor) --}}
            <div>
                <label for="empresa_id" class="mb-1 block text-sm text-green-100">Empresa*</label>
                <select id="empresa_id" name="empresa_id" @class([
                    'w-full rounded-lg border px-3 py-2 bg-white text-gray-900',
                    'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400' => $errors->has(
                        'empresa_id'),
                    'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                        'empresa_id'),
                ])
                    data-old="{{ old('empresa_id', $empresaSelecionadaId) }}" data-url-base="{{ url('empresas') }}">
                    <option value="">Selecione...</option>
                    @foreach ($opcoesEmpresas as $id => $rotulo)
                        <option value="{{ $id }}" @selected(old('empresa_id', $empresaSelecionadaId) == $id)>
                            {{ $rotulo }}
                        </option>
                    @endforeach
                </select>
                @error('empresa_id')
                    <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                @enderror
            </div>

            {{-- Setor (carregado conforme empresa) --}}
            <div>
                <label for="setor_id" class="mb-1 block text-sm text-green-100">Setor*</label>
                <select id="setor_id" name="setor_id" @class([
                    'w-full rounded-lg border px-3 py-2 bg-white text-gray-900',
                    'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400' => $errors->has(
                        'setor_id'),
                    'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                        'setor_id'),
                ])
                    data-old="{{ old('setor_id', $funcionario->setor_id) }}">
                    @if ($opcoesSetores->isNotEmpty())
                        @foreach ($opcoesSetores as $id => $rotulo)
                            <option value="{{ $id }}" @selected(old('setor_id', $funcionario->setor_id) == $id)>
                                {{ $rotulo }}
                            </option>
                        @endforeach
                    @else
                        <option value="">Selecione uma empresa...</option>
                    @endif
                </select>
                @error('setor_id')
                    <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Nome --}}
                <div>
                    <label for="nome" class="mb-1 block text-sm text-green-100">Nome*</label>
                    <input id="nome" name="nome" type="text" maxlength="100"
                        value="{{ old('nome', $funcionario->nome) }}" @class([
                            'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                            'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                                'nome'),
                            'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                                'nome'),
                        ])>
                    @error('nome')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Sobrenome --}}
                <div>
                    <label for="sobrenome" class="mb-1 block text-sm text-green-100">Sobrenome*</label>
                    <input id="sobrenome" name="sobrenome" type="text" maxlength="100"
                        value="{{ old('sobrenome', $funcionario->sobrenome) }}" @class([
                            'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                            'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                                'sobrenome'),
                            'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                                'sobrenome'),
                        ])>
                    @error('sobrenome')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                {{-- CPF (texto simples; máscara fica no show e/ou JS do form, se desejar) --}}
                <div>
                    <label for="cpf" class="mb-1 block text-sm text-green-100">CPF*</label>
                    <input id="cpf" name="cpf" type="text" maxlength="20"
                        value="{{ old('cpf', $funcionario->cpf) }}" @class([
                            'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                            'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                                'cpf'),
                            'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                                'cpf'),
                        ])>
                    @error('cpf')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Matrícula --}}
                <div>
                    <label class="mb-1 block text-sm text-green-100">Matrícula**</label>
                    <input type="text" id="matricula" name="matricula"
                        value="{{ old('matricula', $funcionario->matricula) }}" inputmode="numeric" pattern="\d*"
                        autocomplete="off" @class([
                            'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 disabled:bg-gray-300 disabled:cursor-not-allowed placeholder-gray-500 focus:outline-none',
                            'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                                'matricula'),
                            'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                                'matricula'),
                        ]) placeholder="Somente números">
                    @if ($errors->has('matricula'))
                        <p class="mt-1 text-xs text-red-300">{{ $errors->first('matricula') }}</p>
                    @else
                        <p id="email_help" class="mt-1 text-xs text-green-200">**Obrigatório quando não é terceirizado</p>
                    @endif
                </div>
            </div>

            {{-- Telefone --}}
            <div>
                <label for="telefone" class="mb-1 block text-sm text-green-100">Telefone</label>
                <input id="telefone" name="telefone" type="text" maxlength="20"
                    value="{{ old('telefone', $funcionario->telefone) }}" @class([
                        'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                        'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                            'telefone'),
                        'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                            'telefone'),
                    ])>
                @error('telefone')
                    <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                @enderror
            </div>

            {{-- Flags --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="terceirizado" value="1" @checked(old('terceirizado', $funcionario->terceirizado))
                        class="h-5 w-5 rounded border border-green-700">
                    <span class="text-sm">Terceirizado</span>
                </label>

                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="ativo" value="1" @checked(old('ativo', $funcionario->ativo))
                        class="h-5 w-5 rounded border border-green-700">
                    <span class="text-sm">Ativo</span>
                </label>
            </div>

            {{-- Ações --}}
            <div class="flex items-center justify-between pt-2">
                <a href="{{ url()->previous() }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-green-800/60 bg-transparent px-4 py-2 text-sm hover:bg-green-900/40">
                    <i class="fa-solid fa-arrow-left"></i> Cancelar
                </a>

                <button type="submit" class="px-5 py-2 rounded-lg bg-green-700 hover:bg-green-600 transition font-medium">
                    <i class="fa-solid fa-floppy-disk"></i> Salvar
                </button>
            </div>
        </form>
    </div>

    {{-- Scripts (máscaras e carregamento dinâmico do setor) --}}
    @vite(['resources/js/masks.js', 'resources/js/funcionarios/funcionario-form.js'])
@endsection
