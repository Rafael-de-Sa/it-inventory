{{-- Botão quadrado só com ícone (ações de tabela). Renderiza <a> quando recebe `href`. --}}
@props([
    'icon',
    'label',
    'href' => null,
    'variant' => 'default',
])

@php
    $classes = [
        'inline-flex h-8 w-8 cursor-pointer items-center justify-center rounded-md transition-colors',
        'text-red-600 hover:bg-red-50 hover:text-red-700' => $variant === 'danger',
        'text-ink-subtle hover:bg-brand-50 hover:text-brand-700' => $variant !== 'danger',
    ];
@endphp

@if ($href)
    <a href="{{ $href }}" title="{{ $label }}" aria-label="{{ $label }}" {{ $attributes->class($classes) }}>
        <i class="{{ $icon }} text-sm" aria-hidden="true"></i>
    </a>
@else
    <button title="{{ $label }}" aria-label="{{ $label }}" {{ $attributes->merge(['type' => 'submit'])->class($classes) }}>
        <i class="{{ $icon }} text-sm" aria-hidden="true"></i>
    </button>
@endif
