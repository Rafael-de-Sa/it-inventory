@props([
    'active' => false,
    'activeLabel' => 'Ativo',
    'inactiveLabel' => 'Inativo',
])

<x-ui.badge :tone="$active ? 'success' : 'neutral'" {{ $attributes }}>
    <span @class(['h-1.5 w-1.5 rounded-full', 'bg-brand-600' => $active, 'bg-zinc-400' => !$active]) aria-hidden="true"></span>
    {{ $active ? $activeLabel : $inactiveLabel }}
</x-ui.badge>
