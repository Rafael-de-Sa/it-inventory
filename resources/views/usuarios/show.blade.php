@extends('layouts.main_layout')

@section('content')
    <div class="w-full flex justify-center">
        <div class="w-full max-w-3xl bg-green-900/40 border border-green-800 rounded-2xl shadow-lg p-6 md:p-8 space-y-6">

            <header class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-wide">
                    Usuário —
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

            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                <div class="md:col-span-3">
                    <label for="usuario_id" class="block mb-1 text-sm font-medium text-green-100">ID</label>
                    <input id="usuario_id" type="text" value="{{ $usuario->id }}"
                        class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black
                               cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                        disabled readonly>
                </div>

                <div class="md:col-span-3">
                    <label for="usuario_ativo" class="block mb-1 text-sm font-medium text-green-100">Ativo</label>

                    <div class="h-[42px] flex items-center gap-3">
                        <input id="usuario_ativo" type="checkbox" disabled @checked($usuario->ativo)
                            class="h-5 w-5 rounded border-green-700 bg-gray-300 text-green-600 cursor-default
                                   focus:outline-none focus:ring-0 focus:border-green-700">

                        @if ($usuario->ativo)
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium
                                              border border-green-600/60 bg-green-600/15 text-green-100
                                              ring-1 ring-inset ring-green-400/10 shadow-sm">
                                <i class="fa-solid fa-check-circle text-[10px]"></i>
                                Ativo
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium
                                              border border-gray-500/60 bg-gray-500/15 text-gray-200/80
                                              ring-1 ring-inset ring-gray-400/10 shadow-sm">
                                <i class="fa-solid fa-circle-xmark text-[10px]"></i>
                                Inativo
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div>
                <label for="usuario_funcionario" class="block mb-1 text-sm font-medium text-green-100">
                    Funcionário
                </label>
                <input id="usuario_funcionario" type="text"
                    value="@if ($usuario->funcionario) {{ trim($usuario->funcionario->nome . ' ' . $usuario->funcionario->sobrenome) . ' - ' . $usuario->funcionario->matricula }} @else Não vinculado @endif"
                    class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black
                           cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                    disabled readonly>
            </div>

            <div>
                <label for="usuario_email" class="block mb-1 text-sm font-medium text-green-100">E-mail</label>
                <input id="usuario_email" type="text" value="{{ $usuario->email ?? 'Sem e-mail vinculado' }}"
                    class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black
                           cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                    disabled readonly>
            </div>

            <div>
                <label for="usuario_ultimo_login" class="block mb-1 text-sm font-medium text-green-100">
                    Último login
                </label>
                <input id="usuario_ultimo_login" type="text"
                    value="{{ $usuario->ultimo_login ? $usuario->ultimo_login->format('d/m/Y H:i') : 'Nunca acessou' }}"
                    class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black
                           cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                    disabled readonly>
            </div>

            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('funcionarios.index') }}"
                    class="px-4 py-2 rounded-lg border border-green-700 hover:bg-green-800/40 inline-flex items-center gap-2"
                    title="Voltar" aria-label="Voltar">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Voltar</span>
                </a>

                <div class="flex items-center gap-3">
                    <a href="{{ route('usuarios.edit', $usuario->id) }}"
                        class="px-4 py-2 rounded-lg border border-green-700 text-green-100 hover:bg-green-800/40 inline-flex items-center gap-2 cursor-pointer"
                        title="Editar" aria-label="Editar">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span>Editar</span>
                    </a>

                    @if (auth()->id() != $usuario->id)
                        <form method="POST" action="{{ route('usuarios.destroy', $usuario->id) }}"
                            onsubmit="return confirm('Excluir o usuário {{ addslashes($usuario->email ?? '(sem e-mail)') }}?');"
                            class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="cursor-pointer px-4 py-2 rounded-lg border border-red-700 text-red-200 hover:bg-red-900/30 inline-flex items-center gap-2"
                                aria-label="Excluir">
                                <i class="fa-solid fa-trash"></i>
                                <span>Excluir</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
