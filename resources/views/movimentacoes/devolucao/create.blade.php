@extends('layouts.main_layout')

@section('content')
    <div class="w-full flex justify-center">
        <form id="movimentacaoDevolucaoForm" action="{{ route('movimentacoes.devolucao.store') }}" method="POST"
            class="w-full max-w-6xl bg-green-900/40 border border-green-800 rounded-2xl shadow-lg p-6 md:p-8 space-y-6"
            data-carregar-setores-endpoint="{{ route('movimentacoes.setores-para-movimentacao', ['empresa' => 'EMPRESA_ID']) }}"
            data-carregar-funcionarios-endpoint="{{ route('movimentacoes.funcionarios-para-movimentacao', ['setor' => 'SETOR_ID']) }}"
            data-carregar-equipamentos-em-uso-endpoint="{{ route('movimentacoes.equipamentos-em-uso', ['funcionario' => 'FUNCIONARIO_ID']) }}"
            data-old-empresa-id="{{ old('empresa_id', request('empresa_id')) }}"
            data-old-setor-id="{{ old('setor_id', request('setor_id')) }}"
            data-old-funcionario-id="{{ old('funcionario_id', request('funcionario_id')) }}"
            data-old-equipamentos='@json(old('equipamentos', []))'>
            @csrf

            {{-- Cabeçalho --}}
            <header class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-wide">Registro de Devolução de Equipamentos</h2>
                <p class="text-xs text-green-200">
                    Selecione o funcionário e escolha os equipamentos que serão devolvidos, informando o estado de
                    devolução.
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
                <label for="empresa_id" class="block mb-1 text-sm font-medium text-green-100">Empresa*</label>
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
                        <option value="{{ $empresa->id }}" @selected(old('empresa_id', request('empresa_id')) == $empresa->id)>
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
                        Escolha a empresa à qual o funcionário está vinculado (ou estava vinculado).
                    </p>
                @endif
            </div>

            {{-- Setor --}}
            <div>
                <label for="setor_id" class="block mb-1 text-sm font-medium text-green-100">Setor*</label>
                <select id="setor_id" name="setor_id" @class([
                    'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                    'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                        'setor_id'),
                    'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                        'setor_id'),
                ])
                    aria-invalid="{{ $errors->has('setor_id') ? 'true' : 'false' }}" aria-describedby="setor_id_help"
                    {{ old('empresa_id', request('empresa_id')) ? '' : 'disabled' }}>
                    <option value="">
                        {{ old('empresa_id', request('empresa_id')) ? 'Selecione…' : 'Selecione uma empresa primeiro…' }}
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
                        Após escolher a empresa, selecione o setor atual ou último setor do funcionário.
                    </p>
                @endif
            </div>

            {{-- Funcionário --}}
            <div>
                <label for="funcionario_id" class="block mb-1 text-sm font-medium text-green-100">Funcionário*</label>
                <select id="funcionario_id" name="funcionario_id" @class([
                    'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                    'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                        'funcionario_id'),
                    'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                        'funcionario_id'),
                ])
                    aria-invalid="{{ $errors->has('funcionario_id') ? 'true' : 'false' }}"
                    aria-describedby="funcionario_id_help" {{ old('setor_id', request('setor_id')) ? '' : 'disabled' }}>
                    <option value="">
                        {{ old('setor_id', request('setor_id')) ? 'Selecione…' : 'Selecione um setor primeiro…' }}
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
                        Selecione o funcionário que está devolvendo os equipamentos.
                    </p>
                @endif
            </div>

            {{-- Observação geral (opcional) --}}
            <div>
                <label for="observacao" class="block mb-1 text-sm font-medium text-green-100">Observação geral
                    (opcional)</label>
                <textarea id="observacao" name="observacao" rows="3" @class([
                    'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                    'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                        'observacao'),
                    'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                        'observacao'),
                ])
                    aria-invalid="{{ $errors->has('observacao') ? 'true' : 'false' }}" aria-describedby="observacao_help"
                    placeholder="Detalhes gerais sobre a devolução...">{{ old('observacao') }}</textarea>

                @if ($errors->has('observacao'))
                    <p id="observacao_help" class="mt-1 text-xs text-red-300 flex items-center gap-1">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                        </svg>
                        {{ $errors->first('observacao') }}
                    </p>
                @else
                    <p id="observacao_help" class="mt-1 text-xs text-green-200">
                        Você pode detalhar aqui o contexto geral da devolução (ex.: desligamento, troca de equipamento,
                        etc.).
                    </p>
                @endif
            </div>

            {{-- Bloco de equipamentos em uso --}}
            <section class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-green-100">Equipamentos em uso pelo funcionário</h3>
                    <p class="text-[11px] text-green-200/80">
                        Selecione os equipamentos que serão devolvidos e descreva o estado de devolução.
                    </p>
                </div>

                <div class="rounded-2xl bg-green-900/30 border border-green-800 shadow-sm">
                    <div class="px-4 py-2 border-b border-green-800/60 flex items-center justify-between">
                        <span class="text-sm font-medium text-green-100">Equipamentos vinculados a termos de
                            responsabilidade em aberto</span>
                        <span class="text-[11px] text-green-200/80">
                            A lista é carregada após selecionar o funcionário.
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm table-auto" id="tabela_equipamentos_em_uso">
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
                                    <th class="px-4 py-2">Motivo da devolução</th>
                                    <th class="px-4 py-2">Observação da devolução</th>
                                </tr>
                            </thead>
                            <tbody class="bg-green-950/10">
                                {{-- linhas inseridas via JS --}}
                            </tbody>
                        </table>

                        {{-- Template de linha, controlado pela view, usado pelo JS --}}
                        <template id="linha_equipamento_template">
                            <tr class="border-b border-green-800/30 transition-colors hover:bg-green-800/15"
                                data-equipamento-id="">
                                <td class="px-4 py-2 text-center">
                                    <input type="checkbox" class="checkbox-equipamento">
                                </td>
                                <td class="px-4 py-2 text-center coluna-id"></td>
                                <td class="px-4 py-2 text-center coluna-patrimonio"></td>
                                <td class="px-4 py-2 text-center coluna-numero-serie"></td>
                                <td class="px-4 py-2 text-center coluna-descricao"></td>
                                <td class="px-4 py-2 text-center coluna-tipo"></td>

                                {{-- Motivo da devolução --}}
                                <td class="px-4 py-2">
                                    <select
                                        class="campo-motivo-devolucao w-full rounded-lg border border-green-700 bg-white px-2 py-1.5 text-xs text-gray-900 focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-400">
                                        <option value="">Selecione…</option>
                                        <option value="manutencao">Manutenção</option>
                                        <option value="defeito">Defeito</option>
                                        <option value="quebra">Quebra</option>
                                        <option value="devolucao">Devolução normal</option>
                                    </select>
                                </td>

                                {{-- Observação da devolução --}}
                                <td class="px-4 py-2">
                                    <textarea
                                        class="campo-observacao w-full rounded-lg border border-green-700 bg-white px-2 py-1.5 text-xs text-gray-900 placeholder-gray-500 focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-400 resize-none"
                                        rows="2" placeholder="Descreva o estado em que o equipamento foi devolvido..."></textarea>
                                </td>
                            </tr>
                        </template>



                    </div>
                </div>

                {{-- inputs hidden para equipamentos[] serão inseridos via JS, conforme seleção --}}
                <div id="container_inputs_equipamentos"></div>

                @if ($errors->has('equipamentos'))
                    <p class="mt-1 text-xs text-red-300 flex items-center gap-1">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                        </svg>
                        {{ $errors->first('equipamentos') }}
                    </p>
                @else
                    <p class="mt-1 text-xs text-green-200">
                        Selecione ao menos um equipamento para registrar a devolução.
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
                    <i class="fa-solid fa-rotate-left"></i> Registrar devolução
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/movimentacoes/movimentacao-devolucao-form.js')
@endpush
