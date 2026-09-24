@extends('layouts.main_layout')

@section('content')
    @php $funcionario = $usuario->funcionario; @endphp

    <x-ui.card>
        <x-ui.card-header
            :title="'Usuário — ' . ($funcionario?->nome_completo ?? $usuario->email ?? 'Sem e-mail vinculado')">
            <x-slot:subtitle>
                <x-ui.timestamps :model="$usuario" />
            </x-slot:subtitle>
        </x-ui.card-header>

        <x-form.grid>
            <x-form.readonly id="usuario_id" label="ID" :value="$usuario->id" wrapper-class="md:col-span-3" />
            <x-form.active-toggle id="usuario_ativo" :checked="$usuario->ativo" disabled wrapper-class="md:col-span-3" />
            <x-form.readonly id="usuario_perfil" label="Perfil de acesso" :value="$usuario->perfil?->label()"
                wrapper-class="md:col-span-6" />
        </x-form.grid>

        <x-form.readonly id="usuario_funcionario" label="Funcionário"
            :value="$funcionario ? $funcionario->nome_completo . ' — Matrícula ' . $funcionario->matricula : 'Não vinculado'" />

        <x-form.readonly id="usuario_email" label="E-mail" :value="$usuario->email ?? 'Sem e-mail vinculado'" />

        <x-form.readonly id="usuario_ultimo_login" label="Último login"
            :value="$usuario->ultimo_login?->format('d/m/Y H:i') ?? 'Nunca acessou'" />

        <x-form.actions>
            <x-ui.button :href="route('usuarios.index')" icon="fa-solid fa-arrow-left">Voltar</x-ui.button>

            <div class="flex items-center gap-3">
                <x-ui.button :href="route('usuarios.edit', $usuario)" icon="fa-solid fa-pen-to-square">Editar</x-ui.button>

                {{-- O usuário logado não pode excluir a si mesmo --}}
                @if (auth()->id() != $usuario->id)
                    <x-ui.delete-button :action="route('usuarios.destroy', $usuario)"
                        :confirm="'Excluir o usuário ' . ($usuario->email ?? '(sem e-mail)') . '?'" />
                @endif
            </div>
        </x-form.actions>
    </x-ui.card>
@endsection
