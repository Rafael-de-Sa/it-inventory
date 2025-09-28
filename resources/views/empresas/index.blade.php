@extends('layouts.main_layout')

@section('content')
    <div class="mx-auto w-full max-w-7xl space-y-4">

        {{-- Mensagens de feedback --}}
        @if (session('success'))
            <div class="rounded-lg border border-green-700 bg-green-900/40 px-4 py-3 text-green-100">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="rounded-lg border border-red-700 bg-red-900/30 px-4 py-3 text-red-100">
                {{ session('error') }}
            </div>
        @endif

        {{-- Cabeçalho --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-wide">Empresas</h1>
            </div>

            @can('create', \App\Models\Empresa::class)
                <a href="{{ route('empresas.create') }}"
                    class="inline-flex items-center rounded-lg border border-green-700 bg-green-800/40 px-4 py-2 text-sm hover:bg-green-700/40">
                    Nova Empresa
                </a>
            @endcan
        </div>

        {{-- Filtros (um campo de busca + seleção de coluna + ordenação) --}}
        <form method="GET" class="grid gap-3 rounded-xl border border-green-800 bg-green-900/10 p-3 md:grid-cols-12">
            {{-- Campo (coluna a filtrar) --}}
            <div class="md:col-span-3">
                <label class="mb-1 block text-sm text-green-100">Campo</label>
                @php
                    // Removido 'ativo' da lista de campos buscáveis
                    $mapaCampos = [
                        '' => 'Todos os campos',
                        'id' => 'ID',
                        'nome_fantasia' => 'Nome Fantasia',
                        'razao_social' => 'Razão Social',
                        'cnpj' => 'CNPJ',
                        'email' => 'E-mail',
                        'cidade' => 'Cidade',
                        'estado' => 'UF',
                    ];
                    $campoAtual = request('campo', '');
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
                <input type="text" name="busca" value="{{ old('busca', $termoBusca ?? request('busca')) }}"
                    placeholder="Digite o termo…"
                    class="w-full rounded-lg border border-green-700 bg-white px-3 py-2 text-gray-900 placeholder-gray-500 focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-400">
            </div>

            {{-- Ordenação --}}
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm text-green-100">Ordenar por</label>
                @php
                    // Ordenação pode manter 'ativo' se você permitiu no Request
                    $ordenaveis = [
                        'id' => 'ID',
                        'nome_fantasia' => 'Nome Fantasia',
                        'razao_social' => 'Razão Social',
                        'cnpj' => 'CNPJ',
                        'email' => 'E-mail',
                        'cidade' => 'Cidade',
                        'estado' => 'UF',
                        'ativo' => 'Ativo',
                    ];
                    $colunaAtual = $colunaOrdenacao ?? request('ordenar_por', 'id');
                @endphp
                <select name="ordenar_por"
                    class="w-full rounded-lg border border-green-700 bg-white px-3 py-2 text-gray-900">
                    @foreach ($ordenaveis as $valor => $rotulo)
                        <option value="{{ $valor }}" @selected($colunaAtual === $valor)>{{ $rotulo }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="mb-1 block text-sm text-green-100">Direção</label>
                @php $dirAtual = $direcaoOrdenacao ?? request('direcao', 'asc'); @endphp
                <select name="direcao" class="w-full rounded-lg border border-green-700 bg-white px-3 py-2 text-gray-900">
                    <option value="asc" @selected($dirAtual === 'asc')>Ascendente</option>
                    <option value="desc" @selected($dirAtual === 'desc')>Descendente</option>
                </select>
            </div>

            {{-- Filtro "Ativo" como Ativo/Inativo (dedicado) --}}
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm text-green-100">Ativo</label>
                <select name="ativo" class="w-full rounded-lg border border-green-700 bg-white px-3 py-2 text-gray-900">
                    <option value="">Todos</option>
                    <option value="1" @selected(request('ativo') === '1')>Ativo</option>
                    <option value="0" @selected(request('ativo') === '0')>Inativo</option>
                </select>
            </div>

            <div class="md:col-span-12 flex items-end gap-2">
                <button class="rounded-lg border border-green-700 bg-green-800/40 px-4 py-2 hover:bg-green-700/40">
                    Aplicar
                </button>
                <a href="{{ route('empresas.index') }}" class="rounded-lg border border-green-700 px-4 py-2">
                    Limpar
                </a>
            </div>
        </form>

        {{-- Tabela --}}
        <div class="overflow-x-auto rounded-xl border border-green-800">
            <table class="min-w-full text-sm">
                <thead class="bg-green-900/60 text-green-100 text-center">
                    <tr>
                        <th class="px-4 py-2 ">ID</th>
                        <th class="px-4 py-2 ">Nome Fantasia</th>
                        <th class="px-4 py-2 ">Razão Social</th>
                        <th class="px-4 py-2 ">CNPJ</th>
                        <th class="px-4 py-2 ">E-mail</th>
                        <th class="px-4 py-2 ">Telefone</th>
                        <th class="px-4 py-2 ">Rua</th>
                        <th class="px-4 py-2 ">Número</th>
                        <th class="px-4 py-2 ">Compl.</th>
                        <th class="px-4 py-2 ">Bairro</th>
                        <th class="px-4 py-2 ">CEP</th>
                        <th class="px-4 py-2 ">Cidade</th>
                        <th class="px-4 py-2 ">UF</th>
                        <th class="px-4 py-2 ">Ativo</th>
                        <th class="px-4 py-2 ">Criada em</th>
                        <th class="px-4 py-2 ">Atualizada em</th>
                        <th class="px-4 py-2 ">Ações</th>
                    </tr>
                </thead>

                {{-- Fundo discreto e highlight ao passar o mouse --}}
                <tbody class="bg-green-950/10">
                    @forelse($listaDeEmpresas as $empresaAtual)
                        <tr class="border-b border-green-800/30 transition-colors hover:bg-green-800/15">
                            <td class="px-4 py-2">{{ $empresaAtual->id }}</td>
                            <td class="px-4 py-2">{{ $empresaAtual->nome_fantasia }}</td>
                            <td class="px-4 py-2">{{ $empresaAtual->razao_social }}</td>
                            <td class="px-4 py-2">{{ $empresaAtual->cnpj }}</td>
                            <td class="px-4 py-2">{{ $empresaAtual->email }}</td>
                            <td class="px-4 py-2">{{ $empresaAtual->telefone }}</td>
                            <td class="px-4 py-2">{{ $empresaAtual->rua }}</td>
                            <td class="px-4 py-2">{{ $empresaAtual->numero }}</td>
                            <td class="px-4 py-2">{{ $empresaAtual->complemento }}</td>
                            <td class="px-4 py-2">{{ $empresaAtual->bairro }}</td>
                            <td class="px-4 py-2">{{ $empresaAtual->cep }}</td>
                            <td class="px-4 py-2">{{ $empresaAtual->cidade }}</td>
                            <td class="px-4 py-2">{{ $empresaAtual->estado }}</td>
                            <td class="px-4 py-2">{{ $empresaAtual->ativo ? 'Ativo' : 'Inativo' }}</td>
                            <td class="px-4 py-2">{{ optional($empresaAtual->criado_em)->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-2">{{ optional($empresaAtual->atualizado_em)->format('d/m/Y H:i') }}</td>

                            {{-- Ações: Editar / Excluir --}}
                            <td class="px-4 py-2 text-right">
                                <div class="inline-flex items-center gap-2">
                                    {{-- Editar --}}
                                    <a href="{{ route('empresas.edit', $empresaAtual) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-md no-underline text-current
              hover:bg-green-800/20 focus:outline-none cursor-pointer"
                                        title="Editar" aria-label="Editar">
                                        <i class="fa-solid fa-pen-to-square text-base align-middle" aria-hidden="true"></i>
                                    </a>

                                    {{-- Excluir --}}
                                    <form method="POST" action="{{ route('empresas.destroy', $empresaAtual) }}"
                                        onsubmit="return confirm('Tem certeza que deseja excluir esta empresa?');"
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
                            <td colspan="17" class="px-4 py-6 text-center text-gray-300">
                                Nenhuma empresa encontrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginação --}}
        <div>
            {{ $listaDeEmpresas->onEachSide(1)->links() }}
        </div>

    </div>
@endsection
