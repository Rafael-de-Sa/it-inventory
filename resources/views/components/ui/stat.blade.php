{{--
    Card de indicador do dashboard.
    <x-ui.stat label="Ocorrências abertas" :value="3" icon="fa-solid fa-triangle-exclamation" :href="route('ocorrencias.index')" hint="1 atrasada" tone="warning" />
    tone: neutral | success | warning | danger (cor do ícone)
--}}
@props([
    'label',
    'value',
    'icon' => null,
    'href' => null,
    'hint' => null,
    'tone' => 'neutral',
])

@php
    $corIcone = [
        'neutral' => 'bg-surface-muted text-ink-muted ring-line',
        'success' => 'bg-brand-50 text-brand-700 ring-brand-600/15 dark:text-brand-300',
        'warning' => 'bg-amber-50 text-amber-800 ring-amber-600/20',
        'danger' => 'bg-red-50 text-red-700 ring-red-600/20',
    ][$tone] ?? '';
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if ($href) href="{{ $href }}" @endif
    {{ $attributes->class([
        'flex items-start gap-3 rounded-xl border border-line bg-surface p-4 shadow-sm',
        'transition-colors hover:border-line-strong hover:bg-surface-muted' => $href,
    ]) }}>
    @if ($icon)
        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg ring-1 {{ $corIcone }}">
            <i class="{{ $icon }}" aria-hidden="true"></i>
        </span>
    @endif
    <span class="min-w-0">
        <span class="block text-sm text-ink-muted">{{ $label }}</span>
        <span class="block text-2xl font-semibold tracking-tight text-ink">{{ $value }}</span>
        @if ($hint)
            <span class="block text-xs text-ink-muted">{{ $hint }}</span>
        @endif
    </span>
</{{ $tag }}>
