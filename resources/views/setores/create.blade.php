@extends('layouts.main_layout')

@section('content')
    <div class="mx-auto w-full max-w-7xl space-y-4">

        {{-- Cabeçalho --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-2xl font-semibold tracking-wide">Setores</h1>

            <a href="{{ route('setores.create') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-green-700 bg-green-800/40 px-4 py-2 text-sm hover:bg-green-700/40">
                <i class="fa-solid fa-plus"></i> Novo Setor
            </a>
        </div>

        {{-- Filtros --}}
        <form method="GET" class="grid gap-3 rounded-xl border border-green-800 bg-green-900/10 p-3 md:grid-cols-12">
            {{-- Nome do setor --}}
            <div class="md:col-span-4">
                <label class="mb-1 block text-sm text-green-100">Nome do setor</label>
                <input type="text" name="nome" value="{{ request('nome') }}"
                    class="w-full rounded-lg border border-green-700 bg-white px-3 py-2 text-gray-900 placeholder-gray-500 focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-400"
                    placeholder="Ex.: TI, RH, Financeiro...">
            </div>

            {{-- Empresa (combobox) --}}
            <div class="md:col-span-5">
                <label class="mb-1 block text-sm text-green-100">Empresa</label>
                <select name="empresa_id"
                    class="w-full rounded-lg border border-green-700 bg-white px-3 py-2 text-gray-900">
                    <option value="">Todas</option>
                    @foreach ($opcoesEmpresas as $opt)
                        <option value="{{ $opt->id }}" @selected(request('empresa_id') == $opt->id)>
                            {{ $opt->razao_social }} — {{ \App\Support\Mask::cnpj($opt->cnpj) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Ativo --}}
            <div class="md:col-span-3">
                <label class="mb-1 block text-sm text-green-100">Ativo</label>
                <select name="ativo" class="w-full rounded-lg border border-green-700 bg-white px-3 py-2 text-gray-900">
                    <option value="">Todos</option>
                    <option value="1" @selected(request('ativo') === '1')>Ativo</option>
                    <option value="0" @selected(request('ativo') === '0')>Inativo</option>
                </select>
            </div>

            {{-- Ordenação --}}
            <div class="md:col-span-3">
                <label class="mb-1 block text-sm text-green-100">Ordenar por</label>
                @php $ordenarPorAtual = request('ordenar_por', $ordenarPor ?? 'id'); @endphp
                <select name="ordenar_por"
                    class="w-full rounded-lg border border-green-700 bg-white px-3 py-2 text-gray-900">
                    <option value="id" @selected($ordenarPorAtual === 'id')>ID</option>
                    <option value="nome" @selected($ordenarPorAtual === 'nome')>Nome</option>
                    <option value="empresa_id" @selected($ordenarPorAtual === 'empresa_id')>ID da Empresa</option>
                    <option value="nome_empresa" @selected($ordenarPorAtual === 'nome_empresa')>Nome da Empresa</option>
                    <option value="cnpj_empresa" @selected($ordenarPorAtual === 'cnpj_empresa')>CNPJ da Empresa</option>
                    <option value="ativo" @selected($ordenarPorAtual === 'ativo')>Ativo</option>
                </select>
            </div>

            <div class="md:col-span-3">
                <label class="mb-1 block text-sm text-green-100">Direção</label>
                @php $dirAtual = request('direcao', $direcao ?? 'asc'); @endphp
                <select name="direcao" class="w-full rounded-lg border border-green-700 bg-white px-3 py-2 text-gray-900">
                    <option value="asc" @selected($dirAtual === 'asc')>Ascendente</option>
                    <option value="desc" @selected($dirAtual === 'desc')>Descendente</option>
                </select>
            </div>

            <div class="md:col-span-12 flex flex-wrap items-end justify-between gap-2">
                <a href="{{ route('/') }}"
                    class="rounded-lg border border-green-700 px-4 py-2 hover:bg-green-800/40 inline-flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i><span>Voltar</span>
                </a>

                <div class="flex items-end gap-2">
                    <button
                        class="rounded-lg border border-green-700 bg-green-800/40 px-4 py-2 hover:bg-green-700/40 inline-flex items-center gap-2">
                        <i class="fa-solid fa-filter"></i><span>Aplicar</span>
                    </button>
                    <a href="{{ route('setores.index') }}"
                        class="rounded-lg border border-green-700 px-4 py-2 hover:bg-green-800/40 inline-flex items-center gap-2">
                        <i class="fa-solid fa-rotate-left"></i><span>Limpar</span>
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
                        <th class="px-4 py-2 text-left">Nome do Setor</th>
                        <th class="px-4 py-2 text-left">Nome da Empresa</th>
                        <th class="px-4 py-2 text-left">CNPJ da Empresa</th>
                        <th class="px-4 py-2">Ativo</th>
                        <th class="px-4 py-2">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-green-950/10">
                    @forelse($setores as $setor)
                        <tr class="border-b border-green-800/30 transition-colors hover:bg-green-800/15">
                            <td class="px-4 py-2 text-center">{{ $setor->id }}</td>
                            <td class="px-4 py-2">{{ $setor->nome }}</td>
                            <td class="px-4 py-2">{{ $setor->empresa?->razao_social }}</td>
                            <td class="px-4 py-2">{{ \App\Support\Mask::cnpj($setor->empresa?->cnpj) }}</td>
                            <td class="px-4 py-2 text-center">{{ $setor->ativo ? 'Ativo' : 'Inativo' }}</td>
                            <td class="px-4 py-2 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('setores.show', $setor->id) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-md hover:bg-green-800/20"
                                        title="Exibir" aria-label="Exibir">
                                        <i class="fa-solid fa-eye text-base"></i>
                                    </a>
                                    <a href="{{ route('setores.edit', $setor->id) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-md hover:bg-green-800/20"
                                        title="Editar" aria-label="Editar">
                                        <i class="fa-solid fa-pen-to-square text-base"></i>
                                    </a>
                                    <form method="POST" action="{{ route('setores.destroy', $setor->id) }}"
                                        onsubmit="return confirm('Tem certeza que deseja excluir este setor?');"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="group inline-flex items-center justify-center w-8 h-8 rounded-md hover:bg-red-900/10">
                                            <i
                                                class="fa-solid fa-trash text-base text-red-300 group-hover:text-red-500"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-300">Nenhum setor encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginação --}}
        <div>
            {{ $setores->onEachSide(1)->links() }}
        </div>
    </div>
@endsection
