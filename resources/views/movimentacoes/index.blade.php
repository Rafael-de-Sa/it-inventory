@extends('layouts.main_layout')

@section('content')
    <div class="mx-auto w-full max-w-7xl space-y-4">

        {{-- Cabeçalho --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-wide">Movimentações</h1>
            </div>

            <a href="{{ route('movimentacoes.create') }}"
                class="inline-flex items-center rounded-lg border border-green-700 bg-green-800/40 px-4 py-2 text-sm hover:bg-green-700/40 gap-2">
                <i class="fa-solid fa-plus"></i> Nova Movimentação
            </a>
        </div>

        {{-- Filtros --}}
        <form method="GET" data-endpoints
            data-carregar-setores-endpoint="{{ route('movimentacoes.setores-para-movimentacao', ['empresa' => 'EMPRESA_ID']) }}"
            data-carregar-funcionarios-endpoint="{{ route('movimentacoes.funcionarios-para-movimentacao', ['setor' => 'SETOR_ID']) }}"
            data-old-empresa-id="{{ request('empresa_id') }}" data-old-setor-id="{{ request('setor_id') }}"
            data-old-funcionario-id="{{ request('funcionario_id') }}"
            class="grid gap-3 rounded-xl border border-green-800 bg-green-900/10 p-3 md:grid-cols-12">

            {{-- Busca por ID da movimentação --}}
            <div class="md:col-span-3">
                <label for="busca" class="mb-1 block text-sm text-green-100">ID da movimentação</label>
                <input type="text" id="busca" name="busca"
                    value="{{ old('busca', $termoBusca ?? request('busca')) }}" inputmode="numeric" pattern="\d*"
                    placeholder="Ex.: 1024" @class([
                        'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                        'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400',
                    ])>
            </div>

            {{-- Status --}}
            <div class="md:col-span-3">
                <label for="status" class="mb-1 block text-sm text-green-100">Status</label>
                @php
                    $statusAtual = request('status');
                    // Ajuste conforme os status reais da sua tabela
                    $mapaStatus = [
                        '' => 'Todos',
                        'pendente' => 'Pendente',
                        'concluida' => 'Concluída',
                        'cancelada' => 'Cancelada',
                    ];
                @endphp
                <select id="status" name="status" @class([
                    'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                    'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400',
                ])>
                    @foreach ($mapaStatus as $valor => $rotulo)
                        <option value="{{ $valor }}" @selected((string) $statusAtual === (string) $valor)>
                            {{ $rotulo }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Empresa --}}
            <div class="md:col-span-3">
                <label for="empresa_id" class="mb-1 block text-sm text-green-100">Empresa</label>
                @php $empresaAtual = request('empresa_id'); @endphp
                <select id="empresa_id" name="empresa_id" @class([
                    'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                    'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400',
                ])>
                    <option value="">Todas</option>
                    @foreach ($listaDeEmpresas as $empresa)
                        <option value="{{ $empresa->id }}" @selected((string) $empresaAtual === (string) $empresa->id)>
                            {{ $empresa->rotulo_empresa ?? $empresa->razao_social . ' - ' . $empresa->cnpj_masked }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Setor --}}
            <div class="md:col-span-3">
                <label for="setor_id" class="mb-1 block text-sm text-green-100">Setor</label>
                @php $setorAtual = request('setor_id'); @endphp
                <select id="setor_id" name="setor_id" @class([
                    'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                    'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400',
                ])
                    {{ request('empresa_id') ? '' : 'disabled' }}>
                    <option value="">{{ request('empresa_id') ? 'Todos' : 'Selecione uma empresa…' }}</option>
                    @foreach ($listaDeSetores as $setor)
                        <option value="{{ $setor->id }}" @selected((string) $setorAtual === (string) $setor->id)>
                            {{ $setor->nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Funcionário --}}
            <div class="md:col-span-3">
                <label for="funcionario_id" class="mb-1 block text-sm text-green-100">Funcionário</label>
                @php $funcionarioAtual = request('funcionario_id'); @endphp
                <select id="funcionario_id" name="funcionario_id" @class([
                    'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                    'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400',
                ])
                    {{ request('setor_id') ? '' : 'disabled' }}>
                    <option value="">{{ request('setor_id') ? 'Todos' : 'Selecione um setor…' }}</option>
                    @foreach ($listaDeFuncionarios as $funcionario)
                        <option value="{{ $funcionario->id }}" @selected((string) $funcionarioAtual === (string) $funcionario->id)>
                            {{ trim(($funcionario->nome ?? '') . ' ' . ($funcionario->sobrenome ?? '')) }}
                            @if ($funcionario->matricula)
                                ({{ $funcionario->matricula }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Ordenar por --}}
            <div class="md:col-span-2">
                <label for="ordenar_por" class="mb-1 block text-sm text-green-100">Ordenar por</label>
                @php
                    $ordenaveis = [
                        'data' => 'Data',
                        'id' => 'ID',
                        'status' => 'Status',
                    ];
                    $colunaAtual = $colunaOrdenacao ?? request('ordenar_por', 'data');
                @endphp
                <select id="ordenar_por" name="ordenar_por" @class([
                    'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                    'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400',
                ])>
                    @foreach ($ordenaveis as $valor => $rotulo)
                        <option value="{{ $valor }}" @selected($colunaAtual === $valor)>
                            {{ $rotulo }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Direção --}}
            <div class="md:col-span-2">
                <label for="direcao" class="mb-1 block text-sm text-green-100">Direção</label>
                @php $dirAtual = $direcaoOrdenacao ?? request('direcao', 'desc'); @endphp
                <select id="direcao" name="direcao" @class([
                    'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                    'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400',
                ])>
                    <option value="asc" @selected($dirAtual === 'asc')>Ascendente</option>
                    <option value="desc" @selected($dirAtual === 'desc')>Descendente</option>
                </select>
            </div>

            {{-- Espaço para alinhar os botões no fim --}}
            <div class="md:col-span-9"></div>

            {{-- Botões --}}
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

                    <a href="{{ route('movimentacoes.index') }}"
                        class="rounded-lg border border-green-700 px-4 py-2 hover:bg-green-800/40 inline-flex items-center gap-2">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Limpar</span>
                    </a>
                </div>
            </div>
        </form>

        {{-- Tabela de resultados --}}
        <div class="overflow-x-auto rounded-2xl border border-green-800 bg-green-900/20">
            <table class="min-w-full text-sm table-auto">
                <thead class="bg-green-900/60 text-green-100 text-center">
                    <tr>
                        <th class="px-4 py-2">ID</th>
                        <th class="px-4 py-2">Data</th>
                        <th class="px-4 py-2">Empresa</th>
                        <th class="px-4 py-2">Setor</th>
                        <th class="px-4 py-2">Funcionário</th>
                        <th class="px-4 py-2">Qtd. Equip.</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Ações</th>
                    </tr>
                </thead>

                <tbody class="bg-green-950/10">
                    @forelse ($listaDeMovimentacoes as $movimentacao)
                        @php
                            $empresa = optional($movimentacao->setor)->empresa;
                            $setor = $movimentacao->setor;
                            $funcionario = $movimentacao->funcionario;

                            $status = $movimentacao->status ?? '';
                            $badgeClasses = 'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium';

                            switch ($status) {
                                case 'pendente':
                                    $badgeClasses .= ' bg-yellow-500/20 text-yellow-200 border border-yellow-500/60';
                                    break;
                                case 'concluida':
                                    $badgeClasses .= ' bg-green-500/20 text-green-200 border border-green-500/60';
                                    break;
                                case 'cancelada':
                                    $badgeClasses .= ' bg-red-500/20 text-red-200 border border-red-500/60';
                                    break;
                                default:
                                    $badgeClasses .= ' bg-gray-500/20 text-gray-200 border border-gray-500/60';
                                    break;
                            }
                        @endphp
                        <tr class="border-b border-green-800/30 transition-colors hover:bg-green-800/15">
                            <td class="px-4 py-2 text-center">{{ $movimentacao->id }}</td>
                            <td class="px-4 py-2 text-center">
                                {{ optional($movimentacao->criado_em)->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-2">
                                {{ $empresa?->rotulo_empresa ?? ($empresa?->razao_social ?? '-') }}
                            </td>
                            <td class="px-4 py-2">
                                {{ $setor?->nome ?? '-' }}
                            </td>
                            <td class="px-4 py-2">
                                @if ($funcionario)
                                    {{ trim(($funcionario->nome ?? '') . ' ' . ($funcionario->sobrenome ?? '')) }}
                                    @if ($funcionario->matricula)
                                        ({{ $funcionario->matricula }})
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-2 text-center">
                                {{ $movimentacao->equipamentos->count() }}
                            </td>
                            <td class="px-4 py-2 text-center">
                                <span class="{{ $badgeClasses }}">
                                    {{ $status !== '' ? ucfirst($status) : '-' }}
                                </span>
                            </td>

                            {{-- Ações --}}
                            <td class="px-4 py-2 text-right">
                                <div class="inline-flex items-center gap-2">
                                    {{-- Exibir --}}
                                    <a href="{{ route('movimentacoes.show', $movimentacao->id) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-md no-underline text-current hover:bg-green-800/20 focus:outline-none focus:ring-2 focus:ring-green-500"
                                        title="Exibir" aria-label="Exibir">
                                        <i class="fa-solid fa-eye text-base align-middle" aria-hidden="true"></i>
                                    </a>

                                    {{-- Editar (se fizer sentido no seu fluxo) --}}
                                    <a href="{{ route('movimentacoes.edit', $movimentacao->id) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-md no-underline text-current hover:bg-green-800/20 focus:outline-none cursor-pointer"
                                        title="Editar" aria-label="Editar">
                                        <i class="fa-solid fa-pen-to-square text-base align-middle"
                                            aria-hidden="true"></i>
                                    </a>

                                    {{-- Excluir (se for permitido excluir movimentação) --}}
                                    <form method="POST" action="{{ route('movimentacoes.destroy', $movimentacao->id) }}"
                                        onsubmit="return confirm('Tem certeza que deseja excluir esta movimentação?');"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="group inline-flex items-center justify-center w-8 h-8 rounded-md no-underline hover:bg-red-900/10 focus:outline-none transition-colors cursor-pointer"
                                            title="Excluir" aria-label="Excluir">
                                            <i class="fa-solid fa-trash-can text-base text-red-400 group-hover:text-red-300"
                                                aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-4 text-center text-sm text-green-100/80">
                                Nenhuma movimentação encontrada para os filtros informados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginação --}}
        <div>
            {{ $listaDeMovimentacoes->onEachSide(1)->links() }}
        </div>

    </div>
@endsection

@push('scripts')
    @vite('resources/js/movimentacoes/movimentacao-filtros.js')
@endpush
