@props([
    'active' => false,
    'activeLabel' => 'Ativo',
    'inactiveLabel' => 'Inativo',
])

<span {{ $attributes->class([
    'inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium shadow-sm ring-1 ring-inset',
    'border border-green-600/60 bg-green-600/15 text-green-100 ring-green-400/10' => $active,
    'border border-gray-500/60 bg-gray-500/15 text-gray-200/80 ring-gray-400/10' => !$active,
]) }}>
    <i @class(['fa-solid text-[10px]', 'fa-check-circle' => $active, 'fa-circle-xmark' => !$active])></i>
    {{ $active ? $activeLabel : $inactiveLabel }}
</span>
