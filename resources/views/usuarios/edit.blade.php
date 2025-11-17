@extends('layouts.main_layout')

@section('content')
    <div class="w-full flex justify-center">
        <form id="usuarioEditForm" action="{{ route('usuarios.update', $usuario->id) }}" method="POST"
            class="w-full max-w-3xl bg-green-900/40 border border-green-800 rounded-2xl shadow-lg p-6 md:p-8 space-y-6">
            @csrf
            @method('PUT')

            {{-- Cabeçalho --}}
            <header class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-wide">
                    Editar Usuário —
                    @if ($usuario->funcionario)
                        {{ trim($usuario->funcionario->nome . ' ' . $usuario->funcionario->sobrenome) }}
                    @else
                        {{ $usuario->email ?? 'Sem e-mail vinculado' }}
                    @endif
                </h2>
                <p class="text-xs text-green-200">
                    Criado em: {{ $usuario->criado_em?->format('d/m/Y H:i') }} ·
                    Atualizado em: {{ $usuario->atualizado_em?->format('d/m/Y H:i') }}
                </p>
            </header>

            {{-- Resumo de erros --}}
            @if ($errors->any())
                <div class="rounded-lg border border-red-500/50 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                    <strong>Ops!</strong> Encontramos {{ $errors->count() }} campo(s) para revisar.
                </div>
            @endif

            {{-- ID + Ativo --}}
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                {{-- ID (somente leitura) --}}
                <div class="md:col-span-3">
                    <label for="usuario_id" class="block mb-1 text-sm font-medium text-green-100">ID</label>
                    <input id="usuario_id" type="text" value="{{ $usuario->id }}" @class([
                        'w-full rounded-lg border px-3 py-2 cursor-default',
                        'bg-gray-300 text-black border-green-700',
                    ]) disabled
                        readonly>
                </div>

                {{-- Ativo --}}
                <div class="md:col-span-3">
                    <label for="ativo" class="block mb-1 text-sm font-medium text-green-100">Ativo</label>
                    <div class="h-[42px] flex items-center gap-3">
                        <input type="hidden" name="ativo" value="0">
                        <input id="ativo" name="ativo" type="checkbox" value="1" @checked(old('ativo', $usuario->ativo))
                            @class([
                                'h-5 w-5 rounded focus:ring-0 focus:outline-none',
                                'border-green-700 text-green-700',
                            ])>

                        @if (old('ativo', $usuario->ativo))
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium border border-green-600/60 bg-green-600/15 text-green-100 ring-1 ring-inset ring-green-400/10">
                                <i class="fa-solid fa-check-circle text-[10px]"></i> Ativo
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium border border-gray-500/60 bg-gray-500/15 text-gray-200/80 ring-1 ring-inset ring-gray-400/10">
                                <i class="fa-solid fa-circle-xmark text-[10px]"></i> Inativo
                            </span>
                        @endif
                    </div>
                    @error('ativo')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Funcionário (somente leitura) --}}
            <div>
                <label for="usuario_funcionario" class="block mb-1 text-sm font-medium text-green-100">
                    Funcionário
                </label>

                @php
                    $textoFuncionario = $usuario->funcionario
                        ? trim($usuario->funcionario->nome . ' ' . $usuario->funcionario->sobrenome) .
                            ' — Matrícula ' .
                            $usuario->funcionario->matricula
                        : 'Não vinculado';
                @endphp

                <input id="usuario_funcionario" type="text" value="{{ $textoFuncionario }}"
                    class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black
                           cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                    disabled readonly>
            </div>

            {{-- E-mail + confirmação --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="email" class="block mb-1 text-sm font-medium text-green-100">E-mail*</label>
                    <input id="email" name="email" type="email" maxlength="100"
                        value="{{ old('email', $usuario->email) }}" autocomplete="email" @class([
                            'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                            'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                                'email'),
                            'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                                'email'),
                        ])>
                    @error('email')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email_confirmation" class="block mb-1 text-sm font-medium text-green-100">
                        Confirme o e-mail*
                    </label>
                    <input id="email_confirmation" name="email_confirmation" type="email" maxlength="100"
                        value="{{ old('email_confirmation', $usuario->email) }}" autocomplete="email"
                        @class([
                            'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                            'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                                'email_confirmation'),
                            'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                                'email_confirmation'),
                        ])>
                    @error('email_confirmation')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Senha + confirmação (opcional) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="senha" class="block mb-1 text-sm font-medium text-green-100">Nova senha</label>
                    <input id="senha" name="senha" type="password" autocomplete="new-password"
                        @class([
                            'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                            'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                                'senha'),
                            'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                                'senha'),
                        ])>
                    @if ($errors->has('senha'))
                        <p class="mt-1 text-xs text-red-300">{{ $errors->first('senha') }}</p>
                    @else
                        <p class="mt-1 text-xs text-green-200">
                            Deixe em branco para manter a senha atual. Mínimo 8 caracteres, com letra maiúscula, número e
                            caractere especial.
                        </p>
                    @endif
                </div>

                <div>
                    <label for="senha_confirmation" class="block mb-1 text-sm font-medium text-green-100">
                        Confirme a nova senha
                    </label>
                    <input id="senha_confirmation" name="senha_confirmation" type="password" autocomplete="new-password"
                        @class([
                            'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                            'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                                'senha_confirmation'),
                            'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                                'senha_confirmation'),
                        ])>
                    @error('senha_confirmation')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Ações --}}
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('usuarios.show', $usuario->id) }}"
                    class="px-4 py-2 rounded-lg border border-green-700 hover:bg-green-800/40 inline-flex items-center gap-2"
                    title="Cancelar" aria-label="Cancelar">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Cancelar</span>
                </a>

                <button type="submit"
                    class="px-5 py-2 rounded-lg bg-green-700 hover:bg-green-600 transition font-medium inline-flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Salvar</span>
                </button>
            </div>
        </form>
    </div>
@endsection
