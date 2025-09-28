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
                <p class="text-xs text-green-200">
                    Use a busca e escolha o campo (exceto “Ativo”, que tem filtro próprio). Endereço é exibido em colunas
                    separadas.
                </p>
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
                        'criado_em' => 'Criada em',
                        'atualizado_em' => 'Atualizada em',
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
                <thead class="bg-green-900/60 text-green-100">
                    <tr>
                        <th class="px-4 py-2 text-left">ID</th>
                        <th class="px-4 py-2 text-left">Nome Fantasia</th>
                        <th class="px-4 py-2 text-left">Razão Social</th>
                        <th class="px-4 py-2 text-left">CNPJ</th>
                        <th class="px-4 py-2 text-left">E-mail</th>
                        <th class="px-4 py-2 text-left">Telefone</th>
                        <th class="px-4 py-2 text-left">Rua</th>
                        <th class="px-4 py-2 text-left">Número</th>
                        <th class="px-4 py-2 text-left">Compl.</th>
                        <th class="px-4 py-2 text-left">Bairro</th>
                        <th class="px-4 py-2 text-left">CEP</th>
                        <th class="px-4 py-2 text-left">Cidade</th>
                        <th class="px-4 py-2 text-left">UF</th>
                        <th class="px-4 py-2 text-left">Ativo</th>
                        <th class="px-4 py-2 text-left">Criada em</th>
                        <th class="px-4 py-2 text-left">Atualizada em</th>
                        <th class="px-4 py-2 text-right">Ações</th>
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
                                <div class="inline-flex items-center gap-3">
                                    @can('update', $empresaAtual)
                                        <a class="underline" href="{{ route('empresas.edit', $empresaAtual) }}">Editar</a>
                                    @endcan

                                    @can('delete', $empresaAtual)
                                        <form method="POST" action="{{ route('empresas.destroy', $empresaAtual) }}"
                                            onsubmit="return confirm('Tem certeza que deseja excluir esta empresa?');"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="underline text-red-200 hover:text-red-100">
                                                Excluir
                                            </button>
                                        </form>
                                    @endcan
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
