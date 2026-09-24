@extends('layouts.main_layout')

@section('content')
    @php $funcionario = $usuario->funcionario; @endphp

    <x-form.card id="usuarioEditForm" :action="route('usuarios.update', $usuario)" method="PUT"
        :title="'Editar Usuário — ' . ($funcionario?->nome_completo ?? $usuario->email ?? 'Sem e-mail vinculado')">
        <x-slot:subtitle>
            <x-ui.timestamps :model="$usuario" />
        </x-slot:subtitle>

        <x-form.grid>
            <x-form.readonly id="usuario_id" label="ID" :value="$usuario->id" wrapper-class="md:col-span-3" />

            {{-- O usuário logado não pode inativar a si mesmo --}}
            @if (auth()->id() != $usuario->id)
                <x-form.active-toggle :checked="$usuario->ativo" wrapper-class="md:col-span-3" />
            @else
                <input type="hidden" name="ativo" value="1">
            @endif
        </x-form.grid>

        <x-form.readonly id="usuario_funcionario" label="Funcionário"
            :value="$funcionario ? $funcionario->nome_completo . ' — Matrícula ' . $funcionario->matricula : 'Não vinculado'" />

        <x-form.grid>
            <x-form.input type="email" name="email" label="E-mail" required maxlength="100" autocomplete="email"
                :value="$usuario->email" wrapper-class="md:col-span-6" />
            <x-form.input type="email" name="email_confirmation" label="Confirme o e-mail" required maxlength="100"
                autocomplete="email" :value="$usuario->email" wrapper-class="md:col-span-6" />

            <x-form.input type="password" name="senha" label="Nova senha" autocomplete="new-password"
                help="Deixe em branco para manter a senha atual. Mínimo 8 caracteres, com letra maiúscula, número e caractere especial."
                wrapper-class="md:col-span-6" />
            <x-form.input type="password" name="senha_confirmation" label="Confirme a nova senha"
                autocomplete="new-password" wrapper-class="md:col-span-6" />
        </x-form.grid>

        <x-form.actions>
            <x-ui.button :href="route('usuarios.show', $usuario)" icon="fa-solid fa-arrow-left">Cancelar</x-ui.button>
            <x-ui.button variant="primary" icon="fa-solid fa-floppy-disk">Salvar</x-ui.button>
        </x-form.actions>
    </x-form.card>
@endsection
