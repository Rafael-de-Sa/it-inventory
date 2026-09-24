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
    'space-y-1 rounded-lg border px-4 py-3 text-xs',
    'border-sky-500/70 bg-sky-950/40 text-sky-100' => $variant === 'info',
    'border-amber-500/70 bg-amber-950/40 text-amber-100' => $variant === 'warning',
    'border-gray-500/70 bg-gray-800/40 text-gray-100' => $variant === 'neutral',
    'border-red-500/70 bg-red-950/40 text-red-100' => $variant === 'danger',
]) }}>
    @if ($title)
        <p class="flex items-center gap-2 font-semibold">
            @if ($icon)
                <i class="{{ $icon }}"></i>
            @endif
            <span>{{ $title }}</span>
        </p>
    @endif

    {{ $slot }}
</div>
