@extends('layouts.main_layout')

@section('content')
    <x-ui.page title="Setores">
        <x-slot:actions>
            <x-ui.button :href="route('setores.create')" variant="soft" icon="fa-solid fa-plus" class="text-sm">
                Cadastrar
            </x-ui.button>
        </x-slot:actions>

        <x-form.filters :reset="route('setores.index')">
            <x-form.input name="nome" label="Nome do setor" :value="request('nome')"
                placeholder="Ex.: Recursos Humanos, Financeiro..." wrapper-class="md:col-span-4" />

            <x-form.select name="empresa_id" label="Empresa" placeholder="Todas" :value="request('empresa_id')"
                :options="$opcoesEmpresas"
                :option-label="fn ($empresa) => $empresa->razao_social . ' — ' . \App\Support\Mask::cnpj($empresa->cnpj)"
                wrapper-class="md:col-span-5" />

            <x-form.select name="ativo" label="Ativo" placeholder="Todos" :value="request('ativo')"
                :options="['1' => 'Ativo', '0' => 'Inativo']" wrapper-class="md:col-span-3" />

            <x-form.sort :default="$ordenarPor ?? 'id'" :default-direction="$direcao ?? 'asc'" :options="[
                'id' => 'ID',
                'nome' => 'Nome',
                'nome_empresa' => 'Nome da Empresa',
                'cnpj_empresa' => 'CNPJ da Empresa',
                'ativo' => 'Ativo',
            ]" />
        </x-form.filters>

        <x-table :headers="['ID', 'Nome', 'Nome Empresa', 'CNPJ Empresa', 'Ativo', 'Ações']">
            @forelse ($setores as $setor)
                <x-table.row>
                    <x-table.cell>{{ $setor->id }}</x-table.cell>
                    <x-table.cell>{{ $setor->nome }}</x-table.cell>
                    <x-table.cell>{{ $setor->empresa?->razao_social }}</x-table.cell>
                    <x-table.cell>{{ \App\Support\Mask::cnpj($setor->empresa?->cnpj) }}</x-table.cell>
                    <x-table.cell>{{ $setor->ativo ? 'Ativo' : 'Inativo' }}</x-table.cell>
                    <x-table.actions :show="route('setores.show', $setor)" :edit="route('setores.edit', $setor)"
                        :destroy="route('setores.destroy', $setor)" confirm="Tem certeza que deseja excluir este setor?" />
                </x-table.row>
            @empty
                <x-table.empty :colspan="6">Nenhum setor encontrado.</x-table.empty>
            @endforelse
        </x-table>

        <div>
            {{ $setores->onEachSide(1)->links() }}
        </div>
    </x-ui.page>
@endsection
