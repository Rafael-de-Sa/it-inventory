@extends('layouts.main_layout')

@section('content')
    <div class="mx-auto w-full max-w-7xl space-y-4">

        {{-- Cabeçalho --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-2xl font-semibold tracking-wide">Funcionários</h1>

            <a href="{{ route('funcionarios.create') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-green-700 bg-green-800/40 px-4 py-2 text-sm hover:bg-green-700/40">
                <i class="fa-solid fa-plus"></i> Cadastrar
            </a>
        </div>

        {{-- Filtros --}}
        <form method="GET" class="grid gap-3 rounded-xl border border-green-800 bg-green-900/10 p-3 md:grid-cols-12">
            {{-- Campo --}}
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm text-green-100">Campo</label>
                <select name="campo" @class([
                    'w-full rounded-lg border px-3 py-2 bg-white text-gray-900',
                    'border-green-700' => !$errors->has('campo'),
                    'border-red-500' => $errors->has('campo'),
                ])>
                    @foreach ($opcoesCampo as $valor => $rotulo)
                        <option value="{{ $valor }}" @selected(request('campo') == $valor)>{{ $rotulo }}</option>
                    @endforeach
                </select>
                @error('campo')
                    <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                @enderror
            </div>

            {{-- Busca --}}
            <div class="md:col-span-4">
                <label class="mb-1 block text-sm text-green-100">Busca</label>
                <input type="text" name="busca" value="{{ request('busca') }}" @class([
                    'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                    'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                        'busca'),
                    'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                        'busca'),
                ])
                    placeholder="Digite o termo">
                @error('busca')
                    <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                @enderror
            </div>

            {{-- Ordenar por --}}
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm text-green-100">Ordenar por</label>
                <select name="ordenar_por" @class([
                    'w-full rounded-lg border px-3 py-2 bg-white text-gray-900',
                    'border-green-700' => !$errors->has('ordenar_por'),
                    'border-red-500' => $errors->has('ordenar_por'),
                ])>
                    @foreach ($opcoesOrdenacao as $valor => $rotulo)
                        <option value="{{ $valor }}" @selected(request('ordenar_por') == $valor)>{{ $rotulo }}</option>
                    @endforeach
                </select>
                @error('ordenar_por')
                    <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                @enderror
            </div>

            {{-- Direção --}}
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm text-green-100">Direção</label>
                <select name="direcao" @class([
                    'w-full rounded-lg border px-3 py-2 bg-white text-gray-900',
                    'border-green-700' => !$errors->has('direcao'),
                    'border-red-500' => $errors->has('direcao'),
                ])>
                    <option value="asc" @selected(request('direcao') === 'asc')>Ascendente</option>
                    <option value="desc" @selected(request('direcao') === 'desc')>Descendente</option>
                </select>
                @error('direcao')
                    <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                @enderror
            </div>

            {{-- Ativo --}}
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm text-green-100">Ativo</label>
                <select name="ativo" @class([
                    'w-full rounded-lg border px-3 py-2 bg-white text-gray-900',
                    'border-green-700' => !$errors->has('ativo'),
                    'border-red-500' => $errors->has('ativo'),
                ])>
                    <option value="todos" @selected(request('ativo') === 'todos')>Todos</option>
                    <option value="1" @selected(request('ativo') === '1')>Somente ativos</option>
                    <option value="0" @selected(request('ativo') === '0')>Somente inativos</option>
                </select>
                @error('ativo')
                    <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                @enderror
            </div>

            {{-- Terceirizado (novo combo, igual ao de Ativo) --}}
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm text-green-100">Terceirizado</label>
                <select name="terceirizado" @class([
                    'w-full rounded-lg border px-3 py-2 bg-white text-gray-900',
                    'border-green-700' => !$errors->has('terceirizado'),
                    'border-red-500' => $errors->has('terceirizado'),
                ])>
                    <option value="todos" @selected(request('terceirizado') === 'todos')>Todos</option>
                    <option value="1" @selected(request('terceirizado') === '1')>Somente terceirizados</option>
                    <option value="0" @selected(request('terceirizado') === '0')>Somente próprios</option>
                </select>
                @error('terceirizado')
                    <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                @enderror
            </div>

            {{-- Ações filtro --}}
            <div class="md:col-span-12 flex flex-wrap items-end justify-between gap-2">
                {{-- Voltar para a home --}}
                <a href="{{ route('/') }}"
                    class="rounded-lg border border-green-700 px-4 py-2 hover:bg-green-800/40 inline-flex items-center gap-2"
                    title="Voltar" aria-label="Voltar">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Voltar</span>
                </a>

                <div class="flex items-end gap-2">
                    <button
                        class="rounded-lg border border-green-700 bg-green-800/40 px-4 py-2 hover:bg-green-700/40 inline-flex items-center gap-2">
                        <i class="fa-solid fa-filter"></i>
                        <span>Aplicar</span>
                    </button>

                    <a href="{{ route('funcionarios.index') }}"
                        class="rounded-lg border border-green-700 px-4 py-2 hover:bg-green-800/40 inline-flex items-center gap-2">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Limpar</span>
                    </a>
                </div>
            </div>
        </form>

        {{-- Tabela --}}
        <div class="overflow-x-auto rounded-xl border border-green-800">
            <table class="min-w-full text-sm">
                <thead class="bg-green-900/60 text-green-100 text-center">
                    <tr>
                        <th class="px-4 py-2">ID</th>
                        <th class="px-4 py-2">Nome</th>
                        <th class="px-4 py-2">Empresa</th>
                        <th class="px-4 py-2">CNPJ</th>
                        <th class="px-4 py-2">Setor</th>
                        <th class="px-4 py-2">Matrícula</th>
                        <th class="px-4 py-2">Ativo</th>
                        <th class="px-4 py-2">Terceirizado</th>
                        <th class="px-4 py-2">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-green-900/40 bg-green-900/10">
                    @forelse ($funcionarios as $funcionario)
                        <tr class="text-center">
                            <td class="px-4 py-2">{{ $funcionario->id }}</td>
                            <td class="px-4 py-2">{{ $funcionario->nome }} {{ $funcionario->sobrenome }}</td>
                            <td class="px-4 py-2">{{ $funcionario->empresa_nome ?? '—' }}</td>
                            <td class="px-4 py-2">{{ \App\Support\Mask::cnpj($funcionario->empresa_cnpj) ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $funcionario->setor_nome ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $funcionario->matricula ?? '—' }}</td>

                            <td class="px-4 py-2">{{ $funcionario->ativo ? 'Ativo' : 'Inativo' }}</td>
                            <td class="px-4 py-2">{{ $funcionario->terceirizado ? 'Sim' : 'Não' }}</td>

                            @php
                                $funcionarioPertenceAoUsuarioLogado =
                                    $usuarioLogado &&
                                    $funcionario->usuario &&
                                    $usuarioLogado->id === $funcionario->usuario->id;

                                $podeRealizarDesligamento = $funcionario->podeSerDesligado();

                                $podeMostrarBotaoExcluir =
                                    !$funcionarioPertenceAoUsuarioLogado && $podeRealizarDesligamento;
                            @endphp

                            <td class="px-4 py-2 text-center">
                                <div class="inline-flex items-center gap-2">
                                    {{-- Exibir --}}
                                    <a href="{{ route('funcionarios.show', $funcionario->id) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-md no-underline text-current
          hover:bg-green-800/20 focus:outline-none focus:ring-2 focus:ring-green-500"
                                        title="Exibir" aria-label="Exibir">
                                        <i class="fa-solid fa-eye text-base align-middle" aria-hidden="true"></i>
                                    </a>

                                    {{-- Editar --}}
                                    <a href="{{ route('funcionarios.edit', $funcionario->id) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-md no-underline text-current
              hover:bg-green-800/20 focus:outline-none cursor-pointer"
                                        title="Editar" aria-label="Editar">
                                        <i class="fa-solid fa-pen-to-square text-base align-middle" aria-hidden="true"></i>
                                    </a>

                                    @if ($podeMostrarBotaoExcluir)
                                        {{-- Excluir --}}
                                        <form method="POST" action="{{ route('funcionarios.destroy', $funcionario->id) }}"
                                            onsubmit="return confirm('Tem certeza que deseja excluir este funcionário?');"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="group inline-flex items-center justify-center w-8 h-8 rounded-md no-underline
                     hover:bg-red-900/10 focus:outline-none transition-colors cursor-pointer"
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
                            <td colspan="9" class="px-4 py-6 text-center text-sm text-green-100/80">
                                Nenhum registro encontrado com os filtros aplicados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginação --}}
        <div>
            {{ $funcionarios->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
