@extends('layouts.main_layout')

@section('content')
    <div class="mx-auto w-full max-w-7xl space-y-4">

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-wide">Usuários</h1>
            </div>

            <a href="{{ route('usuarios.create') }}"
                class="inline-flex items-center rounded-lg border border-green-700 bg-green-800/40 px-4 py-2 text-sm hover:bg-green-700/40 gap-2">
                <i class="fa-solid fa-plus"></i>
                <span>Cadastrar</span>
            </a>
        </div>

        <form method="GET" class="grid gap-3 rounded-xl border border-green-800 bg-green-900/10 p-3 md:grid-cols-12">

            <div class="md:col-span-3">
                <label class="mb-1 block text-sm text-green-100">Campo</label>

                <select name="campo" class="w-full rounded-lg border border-green-700 bg-white px-3 py-2 text-gray-900">
                    @foreach ([
            '' => 'Todos os campos',
            'id' => 'ID',
            'funcionario' => 'Nome do Funcionário',
            'email' => 'E-mail',
        ] as $valorCampo => $rotuloCampo)
                        <option value="{{ $valorCampo }}" @selected(request('campo') === (string) $valorCampo)>
                            {{ $rotuloCampo }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-5">
                <label class="mb-1 block text-sm text-green-100">Busca</label>
                <input type="text" name="busca" value="{{ old('busca', $termoBusca ?? request('busca')) }}"
                    placeholder="Digite o termo…"
                    class="w-full rounded-lg border border-green-700 bg-white px-3 py-2 text-gray-900 placeholder-gray-500 focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-400">
            </div>

            <div class="md:col-span-2">
                <label class="mb-1 block text-sm text-green-100">Ordenar por</label>
                <select name="ordenar_por"
                    class="w-full rounded-lg border border-green-700 bg-white px-3 py-2 text-gray-900">
                    @php
                        $colunaAtual = $colunaOrdenacao ?? request('ordenar_por', 'id');
                    @endphp
                    @foreach ([
            'id' => 'ID',
            'funcionario' => 'Nome do Funcionário',
            'email' => 'E-mail',
            'ultimo_login' => 'Último login',
            'ativo' => 'Status',
        ] as $valorOrdenacao => $rotuloOrdenacao)
                        <option value="{{ $valorOrdenacao }}" @selected($colunaAtual === $valorOrdenacao)>
                            {{ $rotuloOrdenacao }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="mb-1 block text-sm text-green-100">Direção</label>
                @php
                    $direcaoAtual = $direcaoOrdenacao ?? request('direcao', 'asc');
                @endphp
                <select name="direcao" class="w-full rounded-lg border border-green-700 bg-white px-3 py-2 text-gray-900">
                    <option value="asc" @selected($direcaoAtual === 'asc')>Ascendente</option>
                    <option value="desc" @selected($direcaoAtual === 'desc')>Descendente</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="mb-1 block text-sm text-green-100">Ativo</label>
                <select name="ativo" class="w-full rounded-lg border border-green-700 bg-white px-3 py-2 text-gray-900">
                    <option value="">Todos</option>
                    <option value="1" @selected(request('ativo') === '1')>Ativo</option>
                    <option value="0" @selected(request('ativo') === '0')>Inativo</option>
                </select>
            </div>

            <div class="md:col-span-12 flex flex-wrap items-end justify-between gap-2">
                <a href="{{ route('/') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-green-700 px-4 py-2 hover:bg-green-800/40"
                    title="Voltar" aria-label="Voltar">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Voltar</span>
                </a>

                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg border border-green-700 px-4 py-2 hover:bg-green-700/40">
                        <i class="fa-solid fa-filter"></i>
                        <span>Aplicar</span>
                    </button>

                    <a href="{{ route('usuarios.index') }}"
                        class="inline-flex items-center gap-2 rounded-lg border border-green-700 px-4 py-2 hover:bg-green-800/40">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Limpar</span>
                    </a>
                </div>
            </div>
        </form>

        <div class="overflow-x-auto rounded-xl border border-green-800">
            <table class="min-w-full table-auto text-sm">
                <thead class="bg-green-900/60 text-center text-green-100">
                    <tr>
                        <th class="px-4 py-2">ID</th>
                        <th class="px-4 py-2">Funcionário</th>
                        <th class="px-4 py-2">E-mail</th>
                        <th class="px-4 py-2">Último login</th>
                        <th class="px-4 py-2">Ativo</th>
                        <th class="px-4 py-2">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-green-950/10">
                    @forelse ($listaDeUsuarios as $usuario)
                        <tr class="border-b border-green-800/30 transition-colors hover:bg-green-800/15">
                            <td class="px-4 py-2 text-center">
                                {{ $usuario->id }}
                            </td>

                            <td class="px-4 py-2 text-center">
                                @if ($usuario->funcionario)
                                    {{ trim($usuario->funcionario->nome . ' ' . $usuario->funcionario->sobrenome) }}
                                @else
                                    <span class="text-xs italic text-gray-400">Não vinculado</span>
                                @endif
                            </td>

                            <td class="px-4 py-2 text-center">
                                @if ($usuario->email)
                                    {{ $usuario->email }}
                                @else
                                    <span class="text-xs italic text-gray-400">Sem e-mail vinculado</span>
                                @endif
                            </td>

                            <td class="px-4 py-2 text-center">
                                @if ($usuario->ultimo_login)
                                    {{ $usuario->ultimo_login->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-xs italic text-gray-400">Nunca acessou</span>
                                @endif
                            </td>

                            <td class="px-4 py-2 text-center">{{ $usuario->ativo ? 'Ativo' : 'Inativo' }}</td>

                            {{-- Ações --}}
                            <td class="px-4 py-2 text-center">
                                <div class="inline-flex items-center gap-2">

                                    <a href="{{ route('usuarios.show', $usuario->id) }}"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-md text-current no-underline hover:bg-green-800/20 focus:outline-none focus:ring-2 focus:ring-green-500"
                                        title="Exibir" aria-label="Exibir">
                                        <i class="fa-solid fa-eye text-base" aria-hidden="true"></i>
                                    </a>

                                    <a href="{{ route('usuarios.edit', $usuario->id) }}"
                                        class="inline-flex h-8 w-8 items-center justify-center cursor-pointer rounded-md text-current no-underline hover:bg-green-800/20 focus:outline-none focus:ring-2 focus:ring-green-500"
                                        title="Editar" aria-label="Editar">
                                        <i class="fa-solid fa-pen-to-square text-base" aria-hidden="true"></i>
                                    </a>

                                    @if (auth()->id() != $usuario->id)
                                        <form method="POST" action="{{ route('usuarios.destroy', $usuario->id) }}"
                                            onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                class="group inline-flex items-center justify-center w-8 h-8 rounded-md no-underline                     hover:bg-red-900/10 focus:outline-none transition-colors cursor-pointer"
                                                title="Excluir" aria-label="Excluir">
                                                <i class="fa-solid fa-trash text-base align-middle text-red-300 group-hover:text-red-500 transition-colors"
                                                    aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-sm text-green-100">
                                Nenhum usuário encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $listaDeUsuarios->onEachSide(1)->links() }}
        </div>
    </div>
@endsection
