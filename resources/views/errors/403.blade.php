@extends('layouts.main_layout')

@section('title', 'Acesso não permitido — IT Inventory')

@section('content')
    <x-ui.card size="sm">
        <div class="flex flex-col items-center space-y-4 py-4 text-center">
            <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-amber-50 text-amber-700 ring-1 ring-amber-600/20">
                <i class="fa-solid fa-lock" aria-hidden="true"></i>
            </span>

            <div class="space-y-1">
                <h1 class="text-xl font-semibold tracking-tight text-ink">Acesso não permitido</h1>
                <p class="text-sm text-ink-muted">
                    O seu perfil não tem permissão para acessar esta área. Se precisar dela, fale com a equipe de TIC.
                </p>
            </div>

            <x-ui.button :href="route('/')" icon="fa-solid fa-house">Voltar ao início</x-ui.button>
        </div>
    </x-ui.card>
@endsection
