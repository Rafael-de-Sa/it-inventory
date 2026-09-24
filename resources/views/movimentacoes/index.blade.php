@extends('layouts.main_layout')

@section('content')
    @php
        $rotuloFuncionario = fn ($funcionario) => $funcionario->nome_completo
            . ($funcionario->matricula ? " ({$funcionario->matricula})" : '');
    @endphp

    <x-ui.page title="Movimentações">
        <x-slot:actions>
            <x-ui.button :href="route('movimentacoes.create')" variant="soft" icon="fa-solid fa-plus" class="text-sm">
                Cadastrar
            </x-ui.button>
        </x-slot:actions>

        {{-- Setor e funcionário são recarregados via JS (movimentacao-filtros.js) conforme empresa/setor. --}}
        <x-form.filters :reset="route('movimentacoes.index')" data-endpoints
            data-carregar-setores-endpoint="{{ route('movimentacoes.setores-para-movimentacao', ['empresa' => 'EMPRESA_ID']) }}"
            data-carregar-funcionarios-endpoint="{{ route('movimentacoes.funcionarios-para-movimentacao', ['setor' => 'SETOR_ID']) }}"
            data-old-empresa-id="{{ request('empresa_id') }}" data-old-setor-id="{{ request('setor_id') }}"
            data-old-funcionario-id="{{ request('funcionario_id') }}">

            <x-form.input name="busca" label="ID da movimentação" :value="$termoBusca ?? request('busca')"
                inputmode="numeric" pattern="\d*" placeholder="Ex.: 1024" wrapper-class="md:col-span-3" />

            <x-form.select name="status" label="Status" placeholder="Todos" :value="request('status')"
                :options="\App\Models\Movimentacao::STATUS" wrapper-class="md:col-span-3" />

            <x-form.select name="empresa_id" label="Empresa" placeholder="Todas" :value="request('empresa_id')"
                :options="$listaDeEmpresas"
                :option-label="fn ($empresa) => $empresa->rotulo_empresa ?? $empresa->razao_social . ' - ' . $empresa->cnpj_masked"
                wrapper-class="md:col-span-3" />

            <x-form.select name="setor_id" label="Setor" :value="request('setor_id')" :disabled="!request('empresa_id')"
                :placeholder="request('empresa_id') ? 'Todos' : 'Selecione uma empresa…'" :options="$listaDeSetores"
                option-label="nome" wrapper-class="md:col-span-3" />

            <x-form.select name="funcionario_id" label="Funcionário" :value="request('funcionario_id')"
                :disabled="!request('setor_id')" :placeholder="request('setor_id') ? 'Todos' : 'Selecione um setor…'"
                :options="$listaDeFuncionarios" :option-label="$rotuloFuncionario" wrapper-class="md:col-span-6" />

            <x-form.sort :default="$colunaOrdenacao ?? 'id'" :default-direction="$direcaoOrdenacao ?? 'asc'"
                :options="['data' => 'Data', 'id' => 'ID', 'status' => 'Status']" />
        </x-form.filters>

        <x-table :headers="['ID', 'Data', 'Tipo', 'Empresa', 'Setor', 'Funcionário', 'Qtd. Equip.', 'Status', 'Ações']">
            @forelse ($listaDeMovimentacoes as $movimentacao)
                @php $empresa = $movimentacao->setor?->empresa; @endphp

                <x-table.row>
                    <x-table.cell>{{ $movimentacao->id }}</x-table.cell>
                    <x-table.cell>{{ $movimentacao->criado_em?->format('d/m/Y H:i') }}</x-table.cell>
                    <x-table.cell>{{ $movimentacao->tipo_rotulo }}</x-table.cell>
                    <x-table.cell>{{ $empresa?->rotulo_empresa ?? ($empresa?->razao_social ?? '-') }}</x-table.cell>
                    <x-table.cell>{{ $movimentacao->setor?->nome ?? '-' }}</x-table.cell>
                    <x-table.cell>{{ $movimentacao->funcionario ? $rotuloFuncionario($movimentacao->funcionario) : '-' }}</x-table.cell>
                    <x-table.cell>{{ $movimentacao->equipamentos->count() }}</x-table.cell>
                    <x-table.cell><x-movimentacao.status :status="$movimentacao->status" /></x-table.cell>
                    <x-table.actions :show="route('movimentacoes.show', $movimentacao)" />
                </x-table.row>
            @empty
                <x-table.empty :colspan="9">Nenhuma movimentação encontrada para os filtros informados.</x-table.empty>
            @endforelse
        </x-table>

        <div>
            {{ $listaDeMovimentacoes->onEachSide(1)->links() }}
        </div>
    </x-ui.page>
@endsection

@push('scripts')
    @vite('resources/js/movimentacoes/movimentacao-filtros.js')
@endpush
