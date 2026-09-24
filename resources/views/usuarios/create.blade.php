@extends('layouts.main_layout')

@section('content')
    <x-form.card id="usuarioForm" :action="route('usuarios.store')" title="Cadastro de Usuário"
        subtitle="Selecione a empresa e o funcionário para criar o acesso ao sistema.">

        {{-- Setor e funcionário são carregados via JS (usuario-form.js) a partir da empresa; data-old restaura após erro. --}}
        <x-form.select name="empresa_id" label="Empresa" required placeholder="Selecione..." :options="$opcoesEmpresas"
            data-old="{{ old('empresa_id') }}" />

        <x-form.select name="setor_id" label="Setor" required placeholder="Selecione uma empresa primeiro..."
            data-old="{{ old('setor_id') }}" />

        <x-form.select name="funcionario_id" label="Funcionário" required placeholder="Selecione um setor primeiro..."
            data-old="{{ old('funcionario_id') }}" />

        <x-form.grid>
            <x-form.input type="email" name="email" label="E-mail" required placeholder="usuario@empresa.com.br"
                wrapper-class="md:col-span-6" />
            <x-form.input type="email" name="email_confirmation" label="Confirmar E-mail" required
                placeholder="Repita o E-mail" wrapper-class="md:col-span-6" />

            <x-form.input type="password" name="senha" label="Senha" required placeholder="Mínimo 8 caracteres"
                autocomplete="new-password" wrapper-class="md:col-span-6" />
            <x-form.input type="password" name="senha_confirmation" label="Confirmar senha" required
                placeholder="Repita a senha" autocomplete="new-password" wrapper-class="md:col-span-6" />
        </x-form.grid>

        <x-form.actions align="end">
            <x-ui.button :href="route('usuarios.index')" icon="fa-solid fa-arrow-left">Cancelar</x-ui.button>
            <x-ui.button variant="primary" icon="fa-solid fa-floppy-disk">Salvar</x-ui.button>
        </x-form.actions>
    </x-form.card>
@endsection

@push('scripts')
    @vite('resources/js/usuarios/usuario-form.js')
@endpush
