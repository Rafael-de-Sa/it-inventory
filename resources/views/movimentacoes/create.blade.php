@extends('layouts.main_layout')

@section('content')
    <div class="w-full flex justify-center">
        <form id="movimentacaoForm" action="{{ route('movimentacoes.store') }}" method="POST"
            class="w-full max-w-6xl bg-green-900/40 border border-green-800 rounded-2xl shadow-lg p-6 md:p-8 space-y-6"
            data-carregar-setores-endpoint="{{ route('movimentacoes.setores-para-movimentacao', ['empresa' => 'EMPRESA_ID']) }}"
            data-carregar-funcionarios-endpoint="{{ route('movimentacoes.funcionarios-para-movimentacao', ['setor' => 'SETOR_ID']) }}"
            data-old-empresa-id="{{ old('empresa_id') }}" data-old-setor-id="{{ old('setor_id') }}"
            data-old-funcionario-id="{{ old('funcionario_id') }}" data-old-equipamentos='@json(old('equipamentos', []))'>
            @csrf

            {{-- Cabeçalho --}}
            <header class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-wide">Cadastro de Movimentação</h2>
                <p class="text-xs text-green-200">
                    Selecione a empresa, setor e funcionário, depois inclua os equipamentos que serão movimentados.
                </p>
            </header>

            {{-- Resumo de erros --}}
            @if ($errors->any())
                <div class="rounded-lg border border-red-500/50 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                    <strong>Ops!</strong> Encontramos {{ $errors->count() }} campo(s) para revisar.
                </div>
            @endif

            {{-- Empresa --}}
            <div>
                <label for="empresa_id" class="block mb-1 text-sm font-medium text-green-100">Empresa</label>
                <select id="empresa_id" name="empresa_id" @class([
                    'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                    'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                        'empresa_id'),
                    'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                        'empresa_id'),
                ])
                    aria-invalid="{{ $errors->has('empresa_id') ? 'true' : 'false' }}" aria-describedby="empresa_id_help">

                    <option value="">Selecione…</option>
                    @foreach ($listaDeEmpresas as $empresa)
                        <option value="{{ $empresa->id }}" @selected(old('empresa_id') == $empresa->id)>
                            {{ $empresa->rotulo_empresa }}
                        </option>
                    @endforeach
                </select>

                @if ($errors->has('empresa_id'))
                    <p id="empresa_id_help" class="mt-1 text-xs text-red-300 flex items-center gap-1">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                        </svg>
                        {{ $errors->first('empresa_id') }}
                    </p>
                @else
                    <p id="empresa_id_help" class="mt-1 text-xs text-green-200">
                        Escolha a empresa à qual o setor e o funcionário pertencem.
                    </p>
                @endif
            </div>

            {{-- Setor --}}
            <div>
                <label for="setor_id" class="block mb-1 text-sm font-medium text-green-100">Setor</label>
                <select id="setor_id" name="setor_id" @class([
                    'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                    'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                        'setor_id'),
                    'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                        'setor_id'),
                ])
                    aria-invalid="{{ $errors->has('setor_id') ? 'true' : 'false' }}" aria-describedby="setor_id_help"
                    {{ old('empresa_id') ? '' : 'disabled' }}>
                    <option value="">
                        {{ old('empresa_id') ? 'Selecione…' : 'Selecione uma empresa primeiro…' }}
                    </option>
                </select>

                @if ($errors->has('setor_id'))
                    <p id="setor_id_help" class="mt-1 text-xs text-red-300 flex items-center gap-1">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                        </svg>
                        {{ $errors->first('setor_id') }}
                    </p>
                @else
                    <p id="setor_id_help" class="mt-1 text-xs text-green-200">
                        Após escolher a empresa, selecione o setor.
                    </p>
                @endif
            </div>

            {{-- Funcionário --}}
            <div>
                <label for="funcionario_id" class="block mb-1 text-sm font-medium text-green-100">Funcionário</label>
                <select id="funcionario_id" name="funcionario_id" @class([
                    'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                    'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                        'funcionario_id'),
                    'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                        'funcionario_id'),
                ])
                    aria-invalid="{{ $errors->has('funcionario_id') ? 'true' : 'false' }}"
                    aria-describedby="funcionario_id_help" {{ old('setor_id') ? '' : 'disabled' }}>
                    <option value="">
                        {{ old('setor_id') ? 'Selecione…' : 'Selecione um setor primeiro…' }}
                    </option>
                </select>

                @if ($errors->has('funcionario_id'))
                    <p id="funcionario_id_help" class="mt-1 text-xs text-red-300 flex items-center gap-1">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                        </svg>
                        {{ $errors->first('funcionario_id') }}
                    </p>
                @else
                    <p id="funcionario_id_help" class="mt-1 text-xs text-green-200">
                        Selecione o funcionário destinatário da movimentação (inclui terceirizados).
                    </p>
                @endif
            </div>

            {{-- Observação (opcional) --}}
            <div>
                <label for="observacao" class="block mb-1 text-sm font-medium text-green-100">Observação (opcional)</label>
                <textarea id="observacao" name="observacao" rows="3" @class([
                    'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                    'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                        'observacao'),
                    'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                        'observacao'),
                ])
                    aria-invalid="{{ $errors->has('observacao') ? 'true' : 'false' }}" aria-describedby="observacao_help"
                    placeholder="Detalhes adicionais sobre a movimentação...">{{ old('observacao') }}</textarea>

                @if ($errors->has('observacao'))
                    <p id="observacao_help" class="mt-1 text-xs text-red-300 flex items-center gap-1">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                        </svg>
                        {{ $errors->first('observacao') }}
                    </p>
                @else
                    <p id="observacao_help" class="mt-1 text-xs text-green-200">
                        Campo opcional para complementar o termo de responsabilidade.
                    </p>
                @endif
            </div>

            {{-- Bloco de equipamentos --}}
            <section class="space-y-4">
                <h3 class="text-lg font-semibold text-green-100">Seleção de Equipamentos</h3>

                {{-- Busca e filtro --}}
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                    <div class="md:col-span-6">
                        <label for="busca_equipamento" class="mb-1 block text-sm text-green-100">Busca equipamento</label>
                        <input type="text" id="busca_equipamento" name="busca_equipamento"
                            placeholder="Patrimônio, número de série ou descrição..."
                            class="w-full rounded-lg border border-green-700 bg-white px-3 py-2 text-gray-900 placeholder-gray-500 focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-400">
                    </div>

                    <div class="md:col-span-4">
                        <label for="filtro_tipo" class="mb-1 block text-sm text-green-100">Filtrar por tipo</label>
                        <select id="filtro_tipo" @class([
                            'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                            'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400',
                        ])>
                            <option value="">Todos os tipos</option>
                        </select>
                    </div>

                    <div class="md:col-span-2 flex md:justify-end">
                        <button type="button" id="botaoPesquisarEquipamentos"
                            class="inline-flex items-center gap-2 rounded-lg border border-green-700 bg-green-800/40 px-4 py-2 text-sm hover:bg-green-700/40">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            Pesquisar
                        </button>
                    </div>
                </div>

                {{-- Tabela de equipamentos disponíveis --}}
                <div class="rounded-2xl bg-green-900/30 border border-green-800 shadow-sm">
                    <div class="px-4 py-2 border-b border-green-800/60 flex items-center justify-between">
                        <span class="text-sm font-medium text-green-100">Tabela de Equipamentos (disponíveis)</span>
                        <span class="text-[11px] text-green-200/80">
                            Selecione os equipamentos e clique em <strong>Adicionar</strong>.
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm table-auto" id="tabela_equipamentos_disponiveis">
                            <thead class="bg-green-900/60 text-green-100">
                                <tr>
                                    <th class="px-4 py-2 text-center w-12">
                                        <span class="sr-only">Selecionar</span>
                                    </th>
                                    <th class="px-4 py-2">ID</th>
                                    <th class="px-4 py-2">Patrimônio</th>
                                    <th class="px-4 py-2">Número de Série</th>
                                    <th class="px-4 py-2">Descrição</th>
                                    <th class="px-4 py-2">Tipo</th>
                                </tr>
                            </thead>
                            <tbody class="bg-green-950/10">
                                @forelse ($listaDeEquipamentos as $equipamento)
                                    <tr class="border-b border-green-800/30 transition-colors hover:bg-green-800/15"
                                        data-equipamento-id="{{ $equipamento->id }}"
                                        data-equipamento-patrimonio="{{ $equipamento->patrimonio }}"
                                        data-equipamento-serie="{{ $equipamento->numero_serie }}"
                                        data-equipamento-descricao="{{ $equipamento->descricao }}"
                                        data-equipamento-tipo="{{ $equipamento->tipoEquipamento->nome ?? '' }}">
                                        <td class="px-4 py-2 text-center">
                                            <input type="checkbox" class="checkbox-equipamento-disponivel">
                                        </td>
                                        <td class="px-4 py-2 text-center">{{ $equipamento->id }}</td>
                                        <td class="px-4 py-2 text-center">{{ $equipamento->patrimonio }}</td>
                                        <td class="px-4 py-2 text-center">{{ $equipamento->numero_serie }}</td>
                                        <td class="px-4 py-2 text-center">{{ $equipamento->descricao }}</td>
                                        <td class="px-4 py-2 text-center">
                                            {{ $equipamento->tipoEquipamento->nome ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-3 text-center text-sm text-green-100/80">
                                            Nenhum equipamento disponível para movimentação.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Botões adicionar/remover --}}
                <div class="flex items-center justify-end gap-3">
                    <button type="button" id="botaoAdicionarEquipamentos"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-green-700 px-4 py-2 text-sm font-medium text-white hover:bg-green-600">
                        <i class="fa-solid fa-plus"></i> Adicionar
                    </button>

                    <button type="button" id="botaoRemoverEquipamentos"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-red-700 px-4 py-2 text-sm font-medium text-white hover:bg-red-600">
                        <i class="fa-solid fa-minus"></i> Remover
                    </button>
                </div>

                {{-- Tabela de equipamentos selecionados --}}
                <div class="rounded-2xl bg-green-900/30 border border-green-800 shadow-sm">
                    <div class="px-4 py-2 border-b border-green-800/60 flex items-center justify-between">
                        <span class="text-sm font-medium text-green-100">Equipamentos selecionados</span>
                        <span class="text-[11px] text-green-200/80">
                            Estes equipamentos serão vinculados à movimentação.
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm table-auto" id="tabela_equipamentos_selecionados">
                            <thead class="bg-green-900/60 text-green-100">
                                <tr>
                                    <th class="px-4 py-2 text-center w-12">
                                        <span class="sr-only">Selecionar</span>
                                    </th>
                                    <th class="px-4 py-2">ID</th>
                                    <th class="px-4 py-2">Patrimônio</th>
                                    <th class="px-4 py-2">Número de Série</th>
                                    <th class="px-4 py-2">Descrição</th>
                                    <th class="px-4 py-2">Tipo</th>
                                </tr>
                            </thead>
                            <tbody class="bg-green-950/10">
                                {{-- preenchido via JS --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- inputs hidden para equipamentos[] --}}
                <div id="container_inputs_equipamentos">
                    {{-- inputs name="equipamentos[]" serão inseridos via JS --}}
                </div>

                @if ($errors->has('equipamentos'))
                    <p class="mt-1 text-xs text-red-300 flex items-center gap-1">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                        </svg>
                        {{ $errors->first('equipamentos') }}
                    </p>
                @else
                    <p class="mt-1 text-xs text-green-200">
                        Selecione ao menos um equipamento para gerar a movimentação.
                    </p>
                @endif
            </section>

            {{-- Ações --}}
            <div class="flex items-center justify-between gap-3 pt-4">
                <a href="{{ route('movimentacoes.index') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-green-700 px-4 py-2 text-sm hover:bg-green-800/40">
                    <i class="fa-solid fa-arrow-left"></i> Cancelar
                </a>

                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-green-700 px-5 py-2 text-sm font-medium text-white hover:bg-green-600">
                    <i class="fa-solid fa-floppy-disk"></i> Gerar Movimentação
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/movimentacoes/movimentacao-form.js')
@endpush
