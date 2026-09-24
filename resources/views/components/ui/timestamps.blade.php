{{-- "Criado em · Atualizado em" de um model, respeitando CREATED_AT/UPDATED_AT customizados. --}}
@props(['model'])

@php
    $format = fn ($date) => $date?->format('d/m/Y H:i') ?? '—';
@endphp

Criado em: {{ $format($model->{$model->getCreatedAtColumn()}) }} ·
Atualizado em: {{ $format($model->{$model->getUpdatedAtColumn()}) }}
