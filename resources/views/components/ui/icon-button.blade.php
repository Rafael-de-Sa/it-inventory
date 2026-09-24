{{-- Botão quadrado só com ícone (ações de tabela). Renderiza <a> quando recebe `href`. --}}
@props([
    'icon',
    'label',
    'href' => null,
    'variant' => 'default',
])

@php
    $classes = [
        'group inline-flex h-8 w-8 cursor-pointer items-center justify-center rounded-md',
        'hover:bg-red-900/10' => $variant === 'danger',
        'hover:bg-green-800/20' => $variant !== 'danger',
    ];
    $iconClasses = [$icon, 'text-base', 'text-red-300 group-hover:text-red-500' => $variant === 'danger'];
@endphp

@if ($href)
    <a href="{{ $href }}" title="{{ $label }}" aria-label="{{ $label }}" {{ $attributes->class($classes) }}>
        <i @class($iconClasses)></i>
    </a>
@else
    <button title="{{ $label }}" aria-label="{{ $label }}" {{ $attributes->merge(['type' => 'submit'])->class($classes) }}>
        <i @class($iconClasses)></i>
    </button>
@endif
