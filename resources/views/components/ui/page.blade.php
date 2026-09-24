{{-- Estrutura de telas de listagem: título + ações (slot `actions`) + conteúdo. --}}
@props(['title'])

<div {{ $attributes->class('mx-auto w-full max-w-7xl space-y-5') }}>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-semibold tracking-tight text-ink">{{ $title }}</h1>

        @isset($actions)
            <div class="flex items-center gap-2">{{ $actions }}</div>
        @endisset
    </div>

    {{ $slot }}
</div>
