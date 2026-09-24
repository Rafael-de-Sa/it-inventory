@extends('layouts.main_layout')

@section('content')
    <x-ui.page title="Tipos de equipamento">
        <x-slot:actions>
            <x-ui.button :href="route('tipo-equipamentos.create')" variant="soft" icon="fa-solid fa-plus" class="text-sm">
                Cadastrar
            </x-ui.button>
        </x-slot:actions>

        <x-form.filters :reset="route('tipo-equipamentos.index')">
            <x-form.select name="campo" label="Campo" :value="request('campo', 'id')"
                :options="['id' => 'ID', 'nome' => 'Nome']" wrapper-class="md:col-span-3" />

            <x-form.input name="busca" label="Busca" :value="request('busca')" placeholder="Digite o termo…"
                wrapper-class="md:col-span-5" />

            <x-form.select name="ativo" label="Ativo" placeholder="Todos" :value="request('ativo')"
                :options="['1' => 'Ativo', '0' => 'Inativo']" wrapper-class="md:col-span-4" />

            <x-form.sort :options="['id' => 'ID', 'nome' => 'Nome']" />
        </x-form.filters>

        <x-table :headers="['ID', 'Nome', 'Ativo', 'Ações']">
            @forelse ($tipos as $tipo)
                <x-table.row>
                    <x-table.cell>{{ $tipo->id }}</x-table.cell>
                    <x-table.cell>{{ $tipo->nome }}</x-table.cell>
                    <x-table.cell>{{ $tipo->ativo ? 'Ativo' : 'Inativo' }}</x-table.cell>
                    <x-table.actions :show="route('tipo-equipamentos.show', $tipo)"
                        :edit="route('tipo-equipamentos.edit', $tipo)" :destroy="route('tipo-equipamentos.destroy', $tipo)"
                        confirm="Tem certeza que deseja remover este tipo?" />
                </x-table.row>
            @empty
                <x-table.empty :colspan="4">Nenhum tipo encontrado.</x-table.empty>
            @endforelse
        </x-table>

        <div>
            {{ $tipos->onEachSide(1)->links() }}
        </div>
    </x-ui.page>
@endsection
