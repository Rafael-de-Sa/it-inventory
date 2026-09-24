@extends('layouts.main_layout')

@section('content')
    <x-ui.page title="Empresas">
        <x-slot:actions>
            <x-ui.button :href="route('empresas.create')" variant="soft" icon="fa-solid fa-plus" class="text-sm">
                Cadastrar
            </x-ui.button>
        </x-slot:actions>

        <x-form.filters :reset="route('empresas.index')">
            <x-form.select name="campo" label="Campo" :value="request('campo', '')" wrapper-class="md:col-span-3"
                :options="[
                    '' => 'Todos os campos',
                    'id' => 'ID',
                    'nome_fantasia' => 'Nome Fantasia',
                    'razao_social' => 'Razão Social',
                    'cnpj' => 'CNPJ',
                    'cidade' => 'Cidade',
                    'estado' => 'UF',
                ]" />

            <x-form.input name="busca" label="Busca" :value="$termoBusca ?? request('busca')" placeholder="Digite o termo…"
                wrapper-class="md:col-span-5" />

            <x-form.select name="ativo" label="Ativo" placeholder="Todos" :value="request('ativo')"
                :options="['1' => 'Ativo', '0' => 'Inativo']" wrapper-class="md:col-span-4" />

            <x-form.sort :default="$colunaOrdenacao ?? 'id'" :default-direction="$direcaoOrdenacao ?? 'asc'" :options="[
                'id' => 'ID',
                'nome_fantasia' => 'Nome Fantasia',
                'razao_social' => 'Razão Social',
                'cnpj' => 'CNPJ',
                'cidade' => 'Cidade',
                'estado' => 'UF',
                'ativo' => 'Ativo',
            ]" />
        </x-form.filters>

        <x-table :headers="['ID', 'Nome Fantasia', 'Razão Social', 'CNPJ', 'Cidade', 'UF', 'Ativo', 'Ações']">
            @forelse ($listaDeEmpresas as $empresa)
                <x-table.row>
                    <x-table.cell>{{ $empresa->id }}</x-table.cell>
                    <x-table.cell>{{ $empresa->nome_fantasia }}</x-table.cell>
                    <x-table.cell>{{ $empresa->razao_social }}</x-table.cell>
                    <x-table.cell>{{ \App\Support\Mask::cnpj($empresa->cnpj) }}</x-table.cell>
                    <x-table.cell>{{ $empresa->cidade }}</x-table.cell>
                    <x-table.cell>{{ $empresa->estado }}</x-table.cell>
                    <x-table.cell>{{ $empresa->ativo ? 'Ativo' : 'Inativo' }}</x-table.cell>
                    <x-table.actions :show="route('empresas.show', $empresa)" :edit="route('empresas.edit', $empresa)"
                        :destroy="route('empresas.destroy', $empresa)"
                        confirm="Tem certeza que deseja excluir esta empresa?" />
                </x-table.row>
            @empty
                <x-table.empty :colspan="8">Nenhuma empresa encontrada.</x-table.empty>
            @endforelse
        </x-table>

        <div>
            {{ $listaDeEmpresas->onEachSide(1)->links() }}
        </div>
    </x-ui.page>
@endsection
