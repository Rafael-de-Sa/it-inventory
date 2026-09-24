{{--
    Caixa de aviso com título opcional. Variantes: info | warning | neutral | danger
    <x-ui.alert variant="warning" icon="fa-solid fa-circle-exclamation" title="Pendências">...</x-ui.alert>
--}}
@props([
    'variant' => 'info',
    'icon' => null,
    'title' => null,
])

<div role="status" {{ $attributes->class([
    'space-y-1 rounded-lg border px-4 py-3 text-sm',
    'border-sky-200 bg-sky-50 text-sky-900' => $variant === 'info',
    'border-amber-200 bg-amber-50 text-amber-900' => $variant === 'warning',
    'border-line bg-surface-muted text-ink-muted' => $variant === 'neutral',
    'border-red-200 bg-red-50 text-red-800' => $variant === 'danger',
]) }}>
    @if ($title)
        <p class="flex items-center gap-2 font-semibold">
            @if ($icon)
                <i class="{{ $icon }}" aria-hidden="true"></i>
            @endif
            <span>{{ $title }}</span>
        </p>
    @endif

    {{ $slot }}
</div>
