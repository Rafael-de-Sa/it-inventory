@extends('layouts.main_layout')

@section('content')
    <div class="w-full flex justify-center">
        <form id="usuarioForm" action="{{ route('usuarios.store') }}" method="POST"
            class="w-full max-w-3xl bg-green-900/40 border border-green-800 rounded-2xl shadow-lg p-6 md:p-8 space-y-6">
            @csrf

            <header class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-wide">Cadastro de Usuário</h2>
                <p class="text-xs text-green-200">
                    Selecione a empresa e o funcionário para criar o acesso ao sistema.
                </p>
            </header>

            @if ($errors->any())
                <div class="rounded-lg border border-red-500/50 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                    <strong>Ops!</strong> Encontramos {{ $errors->count() }} campo(s) para revisar.
                </div>
            @endif

            <div>
                <label class="mb-1 block text-sm text-green-100">Empresa*</label>
                <select id="empresa_id" name="empresa_id" @class([
                    'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                    'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                        'empresa_id'),
                    'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                        'empresa_id'),
                ]) data-old="{{ old('empresa_id') }}"
                    required>
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
                <label class="mb-1 block text-sm text-green-100">Setor*</label>
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

            <div>
                <label class="mb-1 block text-sm text-green-100">Funcionário*</label>
                <select id="funcionario_id" name="funcionario_id" @class([
                    'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                    'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                        'funcionario_id'),
                    'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                        'funcionario_id'),
                ])
                    data-old="{{ old('funcionario_id') }}" required>
                    <option value="">Selecione um setor primeiro...</option>
                </select>
                @error('funcionario_id')
                    <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                @enderror
            </div>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-sm text-green-100">E-mail*</label>
                    <input type="email" name="email" value="{{ old('email') }}" @class([
                        'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                        'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                            'email'),
                        'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                            'email'),
                    ])
                        placeholder="usuario@empresa.com.br" required>
                    @error('email')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm text-green-100">Confirmar E-mail*</label>
                    <input type="email" name="email_confirmation" value="{{ old('email_confirmation') }}"
                        @class([
                            'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                            'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                                'email_confirmation'),
                            'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                                'email_confirmation'),
                        ]) placeholder="Repita o E-mail" required>
                    @error('email_confirmation')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm text-green-100">Senha*</label>
                    <input type="password" name="senha" @class([
                        'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                        'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                            'senha'),
                        'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                            'senha'),
                    ]) placeholder="Mínimo 8 caracteres"
                        autocomplete="new-password" required>
                    @error('senha')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm text-green-100">Confirmar senha*</label>
                    <input type="password" name="senha_confirmation" @class([
                        'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                        'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                            'senha_confirmation'),
                        'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                            'senha_confirmation'),
                    ])
                        placeholder="Repita a senha" autocomplete="new-password" required>
                    @error('senha_confirmation')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('usuarios.index') }}"
                    class="px-4 py-2 rounded-lg border border-green-700 hover:bg-green-800/40 transition">
                    <i class="fa-solid fa-arrow-left"></i> Cancelar
                </a>
                <button type="submit"
                    class="px-5 py-2 rounded-lg bg-green-700 hover:bg-green-600 transition font-medium flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Salvar
                </button>
            </div>
        </form>
    </div>

    @vite(['resources/js/usuarios/usuario-form.js'])
@endsection
