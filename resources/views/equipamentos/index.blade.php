@extends('layouts.main_layout')

@section('content')
    <x-ui.page title="Equipamentos">
        <x-slot:actions>
            <x-ui.button :href="route('equipamentos.create')" variant="soft" icon="fa-solid fa-plus" class="text-sm">
                Cadastrar
            </x-ui.button>
        </x-slot:actions>

        <x-form.filters :reset="route('equipamentos.index')">
            <x-form.select name="campo" label="Campo" :value="$campoFiltro ?? request('campo', '')"
                wrapper-class="md:col-span-3" :options="[
                    '' => 'Todos os campos',
                    'id' => 'ID',
                    'tipo' => 'Tipo do Equipamento',
                    'descricao' => 'Descrição',
                    'patrimonio' => 'Patrimônio',
                    'numero_serie' => 'Número de Série',
                    'status' => 'Status',
                ]" />

            <x-form.input name="busca" label="Busca" :value="$termoBusca ?? request('busca')" placeholder="Digite o termo..."
                wrapper-class="md:col-span-5" />

            <x-form.select name="status" label="Status" :value="$statusFiltro ?? request('status', 'todos')"
                :options="['todos' => 'Todos'] + \App\Models\Equipamento::STATUS" wrapper-class="md:col-span-4" />

            <x-form.sort :default="$ordenarPor ?? 'id'" :default-direction="$direcaoOrdenacao ?? 'asc'" :options="[
                'id' => 'ID',
                'tipo' => 'Tipo Equipamento',
                'patrimonio' => 'Patrimônio',
                'numero_serie' => 'Número de Série',
                'status' => 'Status',
            ]" />
        </x-form.filters>

        <x-table :headers="['ID', 'Tipo Equipamento', 'Descrição', 'Status', 'Patrimônio', 'Número de Série', 'Ações']">
            @forelse ($listaDeEquipamentos as $equipamento)
                <x-table.row>
                    <x-table.cell>{{ $equipamento->id }}</x-table.cell>
                    <x-table.cell>{{ $equipamento->tipoEquipamento?->nome ?? '-' }}</x-table.cell>
                    <x-table.cell>
                        <span class="block max-w-[28rem] truncate" title="{{ $equipamento->descricao }}">
                            {{ \Illuminate\Support\Str::limit($equipamento->descricao ?? '', 80) }}
                        </span>
                    </x-table.cell>
                    <x-table.cell>{{ $equipamento->status_rotulo }}</x-table.cell>
                    <x-table.cell>{{ $equipamento->patrimonio ?? '-' }}</x-table.cell>
                    <x-table.cell>{{ $equipamento->numero_serie ?? '-' }}</x-table.cell>
                    <x-table.actions :show="route('equipamentos.show', $equipamento)"
                        :edit="route('equipamentos.edit', $equipamento)"
                        :destroy="route('equipamentos.destroy', $equipamento)"
                        confirm="Tem certeza que deseja excluir este equipamento?">
                        <x-ui.icon-button :href="route('relatorios.equipamentos.historico', $equipamento)"
                            icon="fa-solid fa-clock-rotate-left" label="Histórico (abre em nova aba)"
                            target="_blank" rel="noopener noreferrer" />
                    </x-table.actions>
                </x-table.row>
            @empty
                <x-table.empty :colspan="7">Nenhum equipamento encontrado.</x-table.empty>
            @endforelse
        </x-table>

        <div>
            {{ $listaDeEquipamentos->onEachSide(1)->links() }}
        </div>
    </x-ui.page>
@endsection
