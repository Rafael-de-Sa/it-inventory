@extends('layouts.main_layout')

@section('content')
    <x-form.card :action="route('login')" size="sm" title="Acessar o sistema"
        subtitle="Informe suas credenciais para continuar.">

        <div class="space-y-4">
            <x-form.input type="email" name="email" label="E-mail" required autocomplete="email" autofocus />
            <x-form.input type="password" name="password" label="Senha" required autocomplete="current-password" />
        </div>

        <x-form.actions align="end">
            <x-ui.button variant="primary" icon="fa-solid fa-right-to-bracket">Entrar</x-ui.button>
        </x-form.actions>
    </x-form.card>
@endsection
