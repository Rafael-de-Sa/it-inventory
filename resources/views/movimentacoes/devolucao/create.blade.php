@extends('layouts.main_layout')

@section('content')
    @php
        // Aceita pré-seleção via query string (ex.: vindo da tela do funcionário) além do old().
        $empresaId = old('empresa_id', request('empresa_id'));
        $setorId = old('setor_id', request('setor_id'));
        $funcionarioId = old('funcionario_id', request('funcionario_id'));
    @endphp

    {{--
        Setor, funcionário e a tabela de equipamentos em uso são carregados via JS
        (movimentacao-devolucao-form.js), que clona #linha_equipamento_template para cada equipamento.
    --}}
    <x-form.card id="movimentacaoDevolucaoForm" :action="route('movimentacoes.devolucao.store')" size="xl"
        title="Registro de Devolução de Equipamentos"
        subtitle="Selecione o funcionário e escolha os equipamentos que serão devolvidos, informando o estado de devolução."
        data-carregar-setores-endpoint="{{ route('movimentacoes.setores-para-movimentacao', ['empresa' => 'EMPRESA_ID']) }}"
        data-carregar-funcionarios-endpoint="{{ route('movimentacoes.funcionarios-para-movimentacao', ['setor' => 'SETOR_ID']) }}"
        data-carregar-equipamentos-em-uso-endpoint="{{ route('movimentacoes.equipamentos-em-uso', ['funcionario' => 'FUNCIONARIO_ID']) }}"
        data-old-empresa-id="{{ $empresaId }}" data-old-setor-id="{{ $setorId }}"
        data-old-funcionario-id="{{ $funcionarioId }}" data-old-equipamentos="{{ json_encode(old('equipamentos', [])) }}">

        <x-form.select name="empresa_id" label="Empresa" required placeholder="Selecione…" :options="$listaDeEmpresas"
            option-label="rotulo_empresa" :value="$empresaId"
            help="Escolha a empresa à qual o funcionário está vinculado (ou estava vinculado)." />

        <x-form.select name="setor_id" label="Setor" required :disabled="!$empresaId"
            :placeholder="$empresaId ? 'Selecione…' : 'Selecione uma empresa primeiro…'"
            help="Após escolher a empresa, selecione o setor atual ou último setor do funcionário." />

        <x-form.select name="funcionario_id" label="Funcionário" required :disabled="!$setorId"
            :placeholder="$setorId ? 'Selecione…' : 'Selecione um setor primeiro…'"
            help="Selecione o funcionário que está devolvendo os equipamentos." />

        <x-form.textarea name="observacao" label="Observação geral (opcional)" rows="3"
            placeholder="Detalhes gerais sobre a devolução..."
            help="Você pode detalhar aqui o contexto geral da devolução (ex.: desligamento, troca de equipamento, etc.)." />

        <section class="space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h3 class="text-lg font-semibold text-green-100">Equipamentos em uso pelo funcionário</h3>
                <p class="text-xs text-green-200/80">
                    Selecione os equipamentos que serão devolvidos e descreva o estado de devolução.
                </p>
            </div>

            <x-table id="tabela_equipamentos_em_uso"
                title="Equipamentos vinculados a termos de responsabilidade em aberto"
                hint="A lista é carregada após selecionar o funcionário.">
                <x-slot:head>
                    <th class="w-12 px-4 py-2"><span class="sr-only">Selecionar</span></th>
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Patrimônio</th>
                    <th class="px-4 py-2">Número de Série</th>
                    <th class="px-4 py-2">Descrição</th>
                    <th class="px-4 py-2">Tipo</th>
                    <th class="px-4 py-2">Motivo da devolução</th>
                    <th class="px-4 py-2">Observação da devolução</th>
                </x-slot:head>
                {{-- Linhas inseridas pelo JS --}}
            </x-table>

            {{-- Linha clonada pelo JS; as classes coluna-*, campo-* e checkbox-equipamento são usadas por ele. --}}
            <template id="linha_equipamento_template">
                <x-table.row data-equipamento-id="">
                    <x-table.cell><input type="checkbox" class="checkbox-equipamento" aria-label="Selecionar equipamento"></x-table.cell>
                    <x-table.cell class="coluna-id"></x-table.cell>
                    <x-table.cell class="coluna-patrimonio"></x-table.cell>
                    <x-table.cell class="coluna-numero-serie"></x-table.cell>
                    <x-table.cell class="coluna-descricao"></x-table.cell>
                    <x-table.cell class="coluna-tipo"></x-table.cell>
                    <x-table.cell class="text-left">
                        <select class="campo-motivo-devolucao form-control h-auto px-2 py-1.5 text-xs"
                            aria-label="Motivo da devolução">
                            <option value="">Selecione…</option>
                            @foreach (\App\Models\MovimentacaoEquipamento::MOTIVOS_SELECIONAVEIS as $motivo)
                                <option value="{{ $motivo }}">{{ \App\Models\MovimentacaoEquipamento::MOTIVOS_DEVOLUCAO[$motivo] }}</option>
                            @endforeach
                        </select>
                    </x-table.cell>
                    <x-table.cell class="text-left">
                        <textarea class="campo-observacao form-control resize-none px-2 py-1.5 text-xs" rows="2"
                            aria-label="Observação da devolução"
                            placeholder="Descreva o estado em que o equipamento foi devolvido..."></textarea>
                    </x-table.cell>
                </x-table.row>
            </template>

            <x-form.field error-key="equipamentos" help="Selecione ao menos um equipamento para registrar a devolução.">
                <div id="container_inputs_equipamentos">{{-- Inputs equipamentos[] inseridos pelo JS --}}</div>
            </x-form.field>
        </section>

        <x-form.actions class="pt-4">
            <x-ui.button :href="route('movimentacoes.index')" icon="fa-solid fa-arrow-left" class="text-sm">Cancelar</x-ui.button>
            <x-ui.button variant="primary" icon="fa-solid fa-rotate-left" class="text-sm">Registrar devolução</x-ui.button>
        </x-form.actions>
    </x-form.card>
@endsection

@push('scripts')
    @vite('resources/js/movimentacoes/movimentacao-devolucao-form.js')
@endpush
