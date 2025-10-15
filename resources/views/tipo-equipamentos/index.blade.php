@extends('layouts.main_layout')

@section('content')
    <div class="mx-auto w-full max-w-7xl space-y-4">

        {{-- Cabeçalho --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-wide">Tipos de equipamento</h1>
            </div>

            <a href="{{ route('tipo-equipamentos.create') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-green-700 bg-green-800/40 px-4 py-2 text-sm hover:bg-green-700/40">
                <i class="fa-solid fa-plus"></i> Cadastrar
            </a>
        </div>

        {{-- Filtros --}}
        <form method="GET" class="grid gap-3 rounded-xl border border-green-800 bg-green-900/10 p-3 md:grid-cols-12">
            {{-- Campo (coluna a filtrar) --}}
            <div class="md:col-span-3">
                <label class="mb-1 block text-sm text-green-100">Campo</label>
                @php
                    $mapaCampos = [
                        'id' => 'ID',
                        'nome' => 'Nome',
                    ];
                    $campoAtual = request('campo', 'id');
                @endphp
                <select name="campo" class="w-full rounded-lg border border-green-700 bg-white px-3 py-2 text-gray-900">
                    @foreach ($mapaCampos as $valor => $rotulo)
                        <option value="{{ $valor }}" @selected($campoAtual === $valor)>{{ $rotulo }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Busca --}}
            <div class="md:col-span-5">
                <label class="mb-1 block text-sm text-green-100">Busca</label>
                <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Digite o termo…"
                    class="w-full rounded-lg border border-green-700 bg-white px-3 py-2 text-gray-900 focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-400">
            </div>

            {{-- Ordenação --}}
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm text-green-100">Ordenar por</label>
                @php
                    $ordenaveis = [
                        'id' => 'ID',
                        'nome' => 'Nome',
                    ];
                    $ordenarAtual = request('ordenar_por', 'id');
                @endphp
                <select name="ordenar_por"
                    class="w-full rounded-lg border border-green-700 bg-white px-3 py-2 text-gray-900">
                    @foreach ($ordenaveis as $valor => $rotulo)
                        <option value="{{ $valor }}" @selected($ordenarAtual === $valor)>{{ $rotulo }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Direção --}}
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm text-green-100">Direção</label>
                @php $dirAtual = request('direcao', 'asc'); @endphp
                <select name="direcao" class="w-full rounded-lg border border-green-700 bg-white px-3 py-2 text-gray-900">
                    <option value="asc" @selected($dirAtual === 'asc')>Ascendente</option>
                    <option value="desc" @selected($dirAtual === 'desc')>Descendente</option>
                </select>
            </div>

            {{-- Status (Ativo) --}}
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm text-green-100">Ativo</label>
                <select name="ativo" class="w-full rounded-lg border border-green-700 bg-white px-3 py-2 text-gray-900">
                    <option value="">Todos</option>
                    <option value="1" @selected(request('ativo') === '1')>Ativo</option>
                    <option value="0" @selected(request('ativo') === '0')>Inativo</option>
                </select>
            </div>

            {{-- Ações dos filtros --}}
            <div class="md:col-span-12 flex flex-wrap items-end justify-between gap-2">
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

                    <a href="{{ route('tipo-equipamentos.index') }}"
                        class="rounded-lg border border-green-700 px-4 py-2 hover:bg-green-800/40 inline-flex items-center gap-2">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Limpar</span>
                    </a>
                </div>

            </div>
        </form>

        {{-- Tabela --}}
        <div class="overflow-x-auto rounded-xl border border-green-800">
            <table class="min-w-full text-sm table-auto">
                <thead class="bg-green-900/60 text-green-100 text-center">
                    <tr>
                        <th class="px-4 py-2">ID</th>
                        <th class="px-4 py-2 text-left">Nome</th>
                        <th class="px-4 py-2">Ativo</th>
                        <th class="px-4 py-2">Ações</th>
                    </tr>
                </thead>

                {{-- Mesmo padrão de fundo/hover do Empresas --}}
                <tbody class="bg-green-950/10">
                    @php
                        // Permite receber $tipos OU $listaDeTipos
                        $listaDeTipos = $listaDeTipos ?? ($tipos ?? collect());
                    @endphp

                    @forelse($listaDeTipos as $tipo)
                        <tr class="border-b border-green-800/30 transition-colors hover:bg-green-800/15">
                            <td class="px-4 py-2 text-center">{{ $tipo->id }}</td>
                            <td class="px-4 py-2">{{ $tipo->nome }}</td>
                            <td class="px-4 py-2 text-center">{{ $tipo->ativo ? 'Ativo' : 'Inativo' }}</td>

                            {{-- Ações padronizadas --}}
                            <td class="px-4 py-2 text-center">
                                <div class="inline-flex items-center justify-center gap-2">
                                    {{-- Exibir --}}
                                    <a href="{{ route('tipo-equipamentos.show', $tipo->id) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-md no-underline text-current hover:bg-green-800/20 focus:outline-none focus:ring-2 focus:ring-green-500"
                                        title="Exibir" aria-label="Exibir">
                                        <i class="fa-solid fa-eye text-base align-middle" aria-hidden="true"></i>
                                    </a>

                                    {{-- Editar --}}
                                    <a href="{{ route('tipo-equipamentos.edit', $tipo->id) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-md no-underline text-current hover:bg-green-800/20 focus:outline-none cursor-pointer"
                                        title="Editar" aria-label="Editar">
                                        <i class="fa-solid fa-pen-to-square text-base align-middle" aria-hidden="true"></i>
                                    </a>

                                    {{-- Remover --}}
                                    <form action="{{ route('tipo-equipamentos.destroy', $tipo->id) }}" method="POST"
                                        class="inline"
                                        onsubmit="return confirm('Tem certeza que deseja remover este tipo?');">
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
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-300">
                                Nenhum tipo encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginação (preserva filtros) --}}
        <div>
            @if ($listaDeTipos instanceof \Illuminate\Pagination\LengthAwarePaginator)
                {{ $listaDeTipos->appends(request()->query())->onEachSide(1)->links() }}
            @endif
        </div>

    </div>
@endsection
