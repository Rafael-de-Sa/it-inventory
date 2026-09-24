{{-- Mensagem de feedback que some sozinha após 5s. Tipos: success | error --}}
@props([
    'type' => 'success',
    'message',
])

@php
    $id = 'flash-' . $type;
    $success = $type === 'success';
@endphp

<div id="{{ $id }}" role="alert" @class([
    'mx-auto mb-6 flex w-full max-w-3xl items-start gap-3 rounded-lg border px-4 py-3 text-sm shadow-sm',
    'border-brand-200 bg-brand-50 text-brand-900' => $success,
    'border-red-200 bg-red-50 text-red-800' => !$success,
])>
    <svg class="mt-0.5 h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
        @if ($success)
            <path fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293A1 1 0 106.293 10.707l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd" />
        @else
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9 5h2v7H9V5zm0 8h2v2H9v-2z"
                clip-rule="evenodd" />
        @endif
    </svg>
    <div class="flex-1">
        <strong class="font-semibold">{{ $success ? 'Sucesso!' : 'Erro!' }}</strong>
        <span>{{ $message }}</span>
    </div>
    <button type="button" class="-m-1 cursor-pointer rounded-md p-1 opacity-70 hover:opacity-100" aria-label="Fechar"
        onclick="document.getElementById('{{ $id }}')?.remove()">✕</button>
</div>
<script>
    setTimeout(() => document.getElementById('{{ $id }}')?.remove(), 5000);
</script>
