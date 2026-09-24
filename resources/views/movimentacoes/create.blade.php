@extends('layouts.main_layout')

@section('content')
    {{--
        Setor/funcionário são carregados via JS (movimentacao-form.js) a partir da empresa.
        O JS move as linhas marcadas entre as duas tabelas e gera os inputs equipamentos[].
    --}}
    <x-form.card id="movimentacaoForm" :action="route('movimentacoes.store')" size="xl" title="Cadastro de Movimentação"
        subtitle="Selecione a empresa, setor e funcionário, depois inclua os equipamentos que serão movimentados."
        data-carregar-setores-endpoint="{{ route('movimentacoes.setores-para-movimentacao', ['empresa' => 'EMPRESA_ID']) }}"
        data-carregar-funcionarios-endpoint="{{ route('movimentacoes.funcionarios-para-movimentacao', ['setor' => 'SETOR_ID']) }}"
        data-old-empresa-id="{{ old('empresa_id') }}" data-old-setor-id="{{ old('setor_id') }}"
        data-old-funcionario-id="{{ old('funcionario_id') }}" data-old-equipamentos="{{ json_encode(old('equipamentos', [])) }}">

        <x-form.select name="empresa_id" label="Empresa" required placeholder="Selecione…" :options="$listaDeEmpresas"
            option-label="rotulo_empresa" help="Escolha a empresa à qual o setor e o funcionário pertencem." />

        <x-form.select name="setor_id" label="Setor" required :disabled="!old('empresa_id')"
            :placeholder="old('empresa_id') ? 'Selecione…' : 'Selecione uma empresa primeiro…'"
            help="Após escolher a empresa, selecione o setor." />

        <x-form.select name="funcionario_id" label="Funcionário" required :disabled="!old('setor_id')"
            :placeholder="old('setor_id') ? 'Selecione…' : 'Selecione um setor primeiro…'"
            help="Selecione o destinatário da movimentação." />

        <x-form.textarea name="observacao" label="Observação (opcional)" rows="3"
            placeholder="Detalhes adicionais sobre a movimentação..."
            help="Campo opcional para complementar o termo de responsabilidade." />

        <section class="space-y-4">
            <h3 class="text-lg font-semibold tracking-tight text-ink">Seleção de Equipamentos<span class="text-red-600" aria-hidden="true">*</span></h3>

            <x-form.grid class="items-end">
                <x-form.input name="busca_equipamento" label="Busca equipamento"
                    placeholder="Patrimônio, número de série ou descrição..." wrapper-class="md:col-span-6" />
                <x-form.select name="filtro_tipo" label="Filtrar por tipo" placeholder="Todos os tipos"
                    wrapper-class="md:col-span-4" />
                <div class="flex md:col-span-2 md:justify-end">
                    <x-ui.button type="button" id="botaoPesquisarEquipamentos" variant="soft"
                        icon="fa-solid fa-magnifying-glass" class="text-sm">Pesquisar</x-ui.button>
                </div>
            </x-form.grid>

            <x-table id="tabela_equipamentos_disponiveis" title="Tabela de equipamentos disponíveis"
                hint="Selecione os equipamentos e clique em Adicionar.">
                <x-slot:head>
                    @include('movimentacoes.partials.cabecalho-equipamentos')
                </x-slot:head>

                @forelse ($listaDeEquipamentos as $equipamento)
                    <x-table.row data-equipamento-id="{{ $equipamento->id }}"
                        data-equipamento-patrimonio="{{ $equipamento->patrimonio }}"
                        data-equipamento-serie="{{ $equipamento->numero_serie }}"
                        data-equipamento-descricao="{{ $equipamento->descricao }}"
                        data-equipamento-tipo="{{ $equipamento->tipoEquipamento->nome ?? '' }}">
                        <x-table.cell>
                            <input type="checkbox" class="checkbox-equipamento-disponivel"
                                aria-label="Selecionar equipamento {{ $equipamento->id }}">
                        </x-table.cell>
                        <x-table.cell>{{ $equipamento->id }}</x-table.cell>
                        <x-table.cell>{{ $equipamento->patrimonio }}</x-table.cell>
                        <x-table.cell>{{ $equipamento->numero_serie }}</x-table.cell>
                        <x-table.cell>{{ $equipamento->descricao }}</x-table.cell>
                        <x-table.cell>{{ $equipamento->tipoEquipamento->nome ?? '-' }}</x-table.cell>
                    </x-table.row>
                @empty
                    <x-table.empty :colspan="6">Nenhum equipamento disponível para movimentação.</x-table.empty>
                @endforelse
            </x-table>

            <div class="flex items-center justify-end gap-3">
                <x-ui.button type="button" id="botaoAdicionarEquipamentos" variant="primary" icon="fa-solid fa-plus"
                    class="text-sm">Adicionar</x-ui.button>
                <x-ui.button type="button" id="botaoRemoverEquipamentos" variant="danger" icon="fa-solid fa-minus"
                    class="text-sm">Remover</x-ui.button>
            </div>

            <x-table id="tabela_equipamentos_selecionados" title="Equipamentos selecionados"
                hint="Estes equipamentos serão vinculados à movimentação.">
                <x-slot:head>
                    @include('movimentacoes.partials.cabecalho-equipamentos')
                </x-slot:head>
                {{-- Preenchida pelo JS --}}
            </x-table>

            <x-form.field error-key="equipamentos" help="Selecione ao menos um equipamento para gerar a movimentação.">
                <div id="container_inputs_equipamentos">{{-- Preenchido pelo JS --}}</div>
            </x-form.field>
        </section>

        <x-form.actions class="pt-4">
            <x-ui.button :href="route('movimentacoes.index')" icon="fa-solid fa-arrow-left" class="text-sm">Cancelar</x-ui.button>
            <x-ui.button variant="primary" icon="fa-solid fa-floppy-disk" class="text-sm">Gerar Movimentação</x-ui.button>
        </x-form.actions>
    </x-form.card>
@endsection

@push('scripts')
    @vite('resources/js/movimentacoes/movimentacao-form.js')
@endpush
