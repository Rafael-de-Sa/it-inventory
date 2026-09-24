{{--
    Botão padrão. Renderiza <a> quando recebe `href`, senão <button>.
    Variantes: primary | secondary | soft | danger | warning
--}}
@props([
    'variant' => 'secondary',
    'icon' => null,
    'href' => null,
])

@php
    $classes = [
        'inline-flex cursor-pointer items-center gap-2 rounded-lg py-2 transition',
        match ($variant) {
            'primary' => 'bg-green-700 px-5 font-medium text-white hover:bg-green-600',
            'soft' => 'border border-green-700 bg-green-800/40 px-4 hover:bg-green-700/40',
            'danger' => 'border border-red-700 px-4 text-red-200 hover:bg-red-900/30',
            'warning' => 'border border-amber-600 px-4 text-amber-100 hover:bg-amber-900/30',
            default => 'border border-green-700 px-4 hover:bg-green-800/40',
        },
    ];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        @if ($icon)
            <i class="{{ $icon }}"></i>
        @endif
        <span>{{ $slot }}</span>
    </a>
@else
    <button {{ $attributes->merge(['type' => 'submit'])->class($classes) }}>
        @if ($icon)
            <i class="{{ $icon }}"></i>
        @endif
        <span>{{ $slot }}</span>
    </button>
@endif
