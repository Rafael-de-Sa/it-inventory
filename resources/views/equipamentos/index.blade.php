@extends('layouts.main_layout')

@section('content')
    <div class="mx-auto w-full max-w-7xl space-y-4">

        {{-- Cabeçalho + Cadastrar (alinhado como Empresas/Setores) --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-2xl font-semibold tracking-wide">Equipamentos</h1>

            <a href="{{ route('equipamentos.create') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-green-700 bg-green-800/40 px-4 py-2 text-sm hover:bg-green-700/40">
                <i class="fa-solid fa-plus"></i> Cadastrar
            </a>
        </div>

        {{-- Filtros (duas linhas, padronizado) --}}
        <form method="GET" class="grid gap-3 rounded-xl border border-green-800 bg-green-900/10 p-3 md:grid-cols-12">

            {{-- Linha 1 --}}
            <div class="md:col-span-3">
                <label class="mb-1 block text-sm text-green-100">Campo</label>
                @php
                    $mapaCampos = [
                        '' => 'Todos os campos',
                        'id' => 'ID',
                        'tipo' => 'Tipo do Equipamento',
                        'descricao' => 'Descrição',
                        'patrimonio' => 'Patrimônio',
                        'numero_serie' => 'Número de Série',
                        'status' => 'Status',
                    ];
                    $campoAtual = $campoFiltro ?? request('campo', '');
                @endphp
                <select name="campo" class="w-full rounded-lg border border-green-700 bg-white px-3 py-2 text-gray-900">
                    @foreach ($mapaCampos as $valor => $rotulo)
                        <option value="{{ $valor }}" @selected($campoAtual === $valor)>{{ $rotulo }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-5">
                <label class="mb-1 block text-sm text-green-100">Busca</label>
                <input type="text" name="busca" value="{{ $termoBusca ?? request('busca') }}"
                    class="w-full rounded-lg border border-green-700 bg-white px-3 py-2 text-gray-900"
                    placeholder="Digite o termo...">
            </div>

            <div class="md:col-span-2">
                <label class="mb-1 block text-sm text-green-100">Ordenar por</label>
                @php
                    $mapaOrdenacao = [
                        'id' => 'ID',
                        'tipo' => 'Tipo Equipamento',
                        'patrimonio' => 'Patrimônio',
                        'numero_serie' => 'Número de Série',
                        'status' => 'Status',
                    ];
                    $ordenacaoAtual = $ordenarPor ?? request('ordenar_por', 'id');
                @endphp
                <select name="ordenar_por"
                    class="w-full rounded-lg border border-green-700 bg-white px-3 py-2 text-gray-900">
                    @foreach ($mapaOrdenacao as $valor => $rotulo)
                        <option value="{{ $valor }}" @selected($ordenacaoAtual === $valor)>{{ $rotulo }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="mb-1 block text-sm text-green-100">Direção</label>
                @php $direcaoAtual = $direcaoOrdenacao ?? request('direcao', 'asc'); @endphp
                <select name="direcao" class="w-full rounded-lg border border-green-700 bg-white px-3 py-2 text-gray-900">
                    <option value="asc" @selected($direcaoAtual === 'asc')>Ascendente</option>
                    <option value="desc" @selected($direcaoAtual === 'desc')>Descendente</option>
                </select>
            </div>

            {{-- Linha 2 (trouxe o Status pra baixo) --}}
            <div class="md:col-span-3">
                <label class="mb-1 block text-sm text-green-100">Status</label>
                @php $statusAtual = $statusFiltro ?? request('status', 'todos'); @endphp
                <select name="status" class="w-full rounded-lg border border-green-700 bg-white px-3 py-2 text-gray-900">
                    <option value="todos" @selected($statusAtual === 'todos')>Todos</option>
                    <option value="disponivel" @selected($statusAtual === 'disponivel')>Disponível</option>
                    <option value="em_uso" @selected($statusAtual === 'em_uso')>Em uso</option>
                    <option value="em_manutencao" @selected($statusAtual === 'em_manutencao')>Em manutenção</option>
                    <option value="defeituoso" @selected($statusAtual === 'defeituoso')>Defeituoso</option>
                    <option value="descartado" @selected($statusAtual === 'descartado')>Descartado</option>
                </select>
            </div>

            {{-- Barra de ações dos filtros (Voltar à esquerda | Aplicar/Limpar à direita) --}}
            <div class="md:col-span-9"></div>
            <div class="md:col-span-12 flex items-end justify-between gap-2">

                <a href="{{ url()->previous() }}"
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

                    <a href="{{ route('equipamentos.index') }}"
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
                        <th class="px-4 py-2">Tipo Equipamento</th>
                        <th class="px-4 py-2">Descrição</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Patrimônio</th>
                        <th class="px-4 py-2">Número de Série</th>
                        <th class="px-4 py-2">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-green-800/60 bg-green-900/10 text-green-50">
                    @forelse ($listaDeEquipamentos as $equipamento)
                        <tr class="hover:bg-green-900/20">
                            <td class="px-4 py-2 text-center">{{ $equipamento->id }}</td>

                            <td class="px-4 py-2 text-center">
                                {{ $equipamento->tipoEquipamento?->nome ?? '-' }}
                            </td>

                            {{-- Descrição resumida com tooltip --}}
                            <td class="px-4 py-2">
                                @php $descricaoCompleta = $equipamento->descricao ?? ''; @endphp
                                <span class="block max-w-[28rem] truncate" title="{{ $descricaoCompleta }}">
                                    {{ \Illuminate\Support\Str::limit($descricaoCompleta, 80) }}
                                </span>
                            </td>

                            <td class="px-4 py-2 text-center">
                                @php
                                    $mapaStatus = [
                                        'disponivel' => 'Disponível',
                                        'em_uso' => 'Em uso',
                                        'em_manutencao' => 'Em manutenção',
                                        'defeituoso' => 'Defeituoso',
                                        'descartado' => 'Descartado',
                                    ];
                                @endphp
                                {{ $mapaStatus[$equipamento->status] ?? $equipamento->status }}
                            </td>

                            <td class="px-4 py-2 text-center">{{ $equipamento->patrimonio ?? '-' }}</td>
                            <td class="px-4 py-2 text-center">{{ $equipamento->numero_serie ?? '-' }}</td>

                            {{-- Ações com hover padronizado (ícones, borda, fundo e transição) --}}
                            <td class="px-4 py-2 text-right">
                                <div class="inline-flex items-center gap-2">
                                    {{-- Exibir --}}
                                    <a href="{{ route('equipamentos.show', $equipamento->id) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-md no-underline text-current
          hover:bg-green-800/20 focus:outline-none focus:ring-2 focus:ring-green-500"
                                        title="Exibir" aria-label="Exibir">
                                        <i class="fa-solid fa-eye text-base align-middle" aria-hidden="true"></i>
                                    </a>

                                    {{-- Editar --}}
                                    <a href="{{ route('equipamentos.edit', $equipamento->id) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-md no-underline text-current
              hover:bg-green-800/20 focus:outline-none cursor-pointer"
                                        title="Editar" aria-label="Editar">
                                        <i class="fa-solid fa-pen-to-square text-base align-middle" aria-hidden="true"></i>
                                    </a>

                                    {{-- Excluir --}}
                                    <form method="POST" action="{{ route('equipamentos.destroy', $equipamento->id) }}"
                                        onsubmit="return confirm('Tem certeza que deseja excluir este equipamento?');"
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
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-green-200">
                                Nenhum equipamento encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginação --}}
        <div>
            {{ $listaDeEquipamentos->onEachSide(1)->links() }}
        </div>
    </div>
@endsection
