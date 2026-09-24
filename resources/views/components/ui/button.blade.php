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
        'inline-flex h-10 cursor-pointer items-center justify-center gap-2 rounded-lg border px-4 text-sm font-medium shadow-xs transition-colors disabled:cursor-not-allowed disabled:opacity-60',
        match ($variant) {
            'primary' => 'border-brand-700 bg-brand-700 text-white hover:border-brand-800 hover:bg-brand-800',
            'soft' => 'border-brand-200 bg-brand-50 text-brand-800 hover:bg-brand-100',
            'danger' => 'border-red-200 bg-surface text-red-700 hover:border-red-300 hover:bg-red-50',
            'warning' => 'border-amber-200 bg-surface text-amber-800 hover:border-amber-300 hover:bg-amber-50',
            default => 'border-line-strong bg-surface text-ink hover:bg-surface-muted hover:border-ink-subtle/40',
        },
    ];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        @if ($icon)
            <i class="{{ $icon }} text-[0.9em]" aria-hidden="true"></i>
        @endif
        <span>{{ $slot }}</span>
    </a>
@else
    <button {{ $attributes->merge(['type' => 'submit'])->class($classes) }}>
        @if ($icon)
            <i class="{{ $icon }} text-[0.9em]" aria-hidden="true"></i>
        @endif
        <span>{{ $slot }}</span>
    </button>
@endif
