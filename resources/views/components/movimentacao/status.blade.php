{{-- Badge do status de uma movimentação: <x-movimentacao.status :status="$movimentacao->status" /> --}}
@props(['status'])

@php
    $tom = match ($status) {
        'pendente' => 'warning',
        'concluida' => 'info',
        'encerrada' => 'success',
        'cancelada' => 'danger',
        default => 'neutral',
    };
    $rotulo = \App\Models\Movimentacao::STATUS[$status] ?? ($status ?: '-');
@endphp

<x-ui.badge :tone="$tom" {{ $attributes }}>{{ $rotulo }}</x-ui.badge>
