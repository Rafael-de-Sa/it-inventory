@extends('layouts.main_layout')

@section('content')
    <x-ui.page title="Funcionários">
        <x-slot:actions>
            <x-ui.button :href="route('funcionarios.create')" variant="soft" icon="fa-solid fa-plus" class="text-sm">
                Cadastrar
            </x-ui.button>
        </x-slot:actions>

        <x-form.filters :reset="route('funcionarios.index')">
            <x-form.select name="campo" label="Campo" :value="request('campo')" :options="$opcoesCampo"
                wrapper-class="md:col-span-2" />

            <x-form.input name="busca" label="Busca" :value="request('busca')" placeholder="Digite o termo"
                wrapper-class="md:col-span-4" />

            <x-form.select name="ativo" label="Ativo" :value="request('ativo', 'todos')" wrapper-class="md:col-span-3"
                :options="['todos' => 'Todos', '1' => 'Somente ativos', '0' => 'Somente inativos']" />

            <x-form.select name="terceirizado" label="Terceirizado" :value="request('terceirizado', 'todos')"
                wrapper-class="md:col-span-3"
                :options="['todos' => 'Todos', '1' => 'Somente terceirizados', '0' => 'Somente próprios']" />

            <x-form.sort :options="$opcoesOrdenacao" />
        </x-form.filters>

        <x-table :headers="['ID', 'Nome', 'Empresa', 'CNPJ', 'Setor', 'Matrícula', 'Ativo', 'Terceirizado', 'Ações']">
            @forelse ($funcionarios as $funcionario)
                @php
                    // Não pode excluir o próprio cadastro nem funcionário com pendências de desligamento.
                    $pertenceAoUsuarioLogado = $usuarioLogado && $funcionario->usuario?->id === $usuarioLogado->id;
                    $podeExcluir = !$pertenceAoUsuarioLogado && $funcionario->podeSerDesligado();
                @endphp

                <x-table.row>
                    <x-table.cell>{{ $funcionario->id }}</x-table.cell>
                    <x-table.cell>{{ $funcionario->nome_completo }}</x-table.cell>
                    <x-table.cell>{{ $funcionario->empresa_nome ?? '—' }}</x-table.cell>
                    <x-table.cell>{{ \App\Support\Mask::cnpj($funcionario->empresa_cnpj) ?: '—' }}</x-table.cell>
                    <x-table.cell>{{ $funcionario->setor_nome ?? '—' }}</x-table.cell>
                    <x-table.cell>{{ $funcionario->matricula ?? '—' }}</x-table.cell>
                    <x-table.cell>{{ $funcionario->ativo ? 'Ativo' : 'Inativo' }}</x-table.cell>
                    <x-table.cell>{{ $funcionario->terceirizado ? 'Sim' : 'Não' }}</x-table.cell>
                    <x-table.actions :show="route('funcionarios.show', $funcionario)"
                        :edit="route('funcionarios.edit', $funcionario)"
                        :destroy="$podeExcluir ? route('funcionarios.destroy', $funcionario) : null"
                        confirm="Tem certeza que deseja excluir este funcionário?" />
                </x-table.row>
            @empty
                <x-table.empty :colspan="9">Nenhum registro encontrado com os filtros aplicados.</x-table.empty>
            @endforelse
        </x-table>

        <div>
            {{ $funcionarios->appends(request()->query())->onEachSide(1)->links() }}
        </div>
    </x-ui.page>
@endsection
