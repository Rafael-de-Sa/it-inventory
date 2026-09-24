@extends('layouts.main_layout')

@section('content')
    <x-ui.page title="Usuários">
        <x-slot:actions>
            <x-ui.button :href="route('usuarios.create')" variant="soft" icon="fa-solid fa-plus" class="text-sm">
                Cadastrar
            </x-ui.button>
        </x-slot:actions>

        <x-form.filters :reset="route('usuarios.index')">
            <x-form.select name="campo" label="Campo" :value="request('campo', '')" wrapper-class="md:col-span-3"
                :options="[
                    '' => 'Todos os campos',
                    'id' => 'ID',
                    'funcionario' => 'Nome do Funcionário',
                    'email' => 'E-mail',
                ]" />

            <x-form.input name="busca" label="Busca" :value="$termoBusca ?? request('busca')" placeholder="Digite o termo…"
                wrapper-class="md:col-span-5" />

            <x-form.select name="ativo" label="Ativo" placeholder="Todos" :value="request('ativo')"
                :options="['1' => 'Ativo', '0' => 'Inativo']" wrapper-class="md:col-span-4" />

            <x-form.sort :default="$colunaOrdenacao ?? 'id'" :default-direction="$direcaoOrdenacao ?? 'asc'" :options="[
                'id' => 'ID',
                'funcionario' => 'Nome do Funcionário',
                'email' => 'E-mail',
                'ultimo_login' => 'Último login',
                'ativo' => 'Status',
            ]" />
        </x-form.filters>

        <x-table :headers="['ID', 'Funcionário', 'E-mail', 'Último login', 'Ativo', 'Ações']">
            @forelse ($listaDeUsuarios as $usuario)
                <x-table.row>
                    <x-table.cell>{{ $usuario->id }}</x-table.cell>
                    <x-table.cell>
                        {{ $usuario->funcionario?->nome_completo }}
                        @unless ($usuario->funcionario)
                            <x-ui.muted>Não vinculado</x-ui.muted>
                        @endunless
                    </x-table.cell>
                    <x-table.cell>
                        {{ $usuario->email }}
                        @unless ($usuario->email)
                            <x-ui.muted>Sem e-mail vinculado</x-ui.muted>
                        @endunless
                    </x-table.cell>
                    <x-table.cell>
                        {{ $usuario->ultimo_login?->format('d/m/Y H:i') }}
                        @unless ($usuario->ultimo_login)
                            <x-ui.muted>Nunca acessou</x-ui.muted>
                        @endunless
                    </x-table.cell>
                    <x-table.cell>{{ $usuario->ativo ? 'Ativo' : 'Inativo' }}</x-table.cell>
                    {{-- O usuário logado não pode excluir a si mesmo --}}
                    <x-table.actions :show="route('usuarios.show', $usuario)" :edit="route('usuarios.edit', $usuario)"
                        :destroy="auth()->id() != $usuario->id ? route('usuarios.destroy', $usuario) : null"
                        confirm="Tem certeza que deseja excluir este usuário?" />
                </x-table.row>
            @empty
                <x-table.empty :colspan="6">Nenhum usuário encontrado.</x-table.empty>
            @endforelse
        </x-table>

        <div>
            {{ $listaDeUsuarios->onEachSide(1)->links() }}
        </div>
    </x-ui.page>
@endsection
