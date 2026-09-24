{{-- Rodapé de ações. `align`: between (padrão) | end --}}
@props(['align' => 'between'])

<div {{ $attributes->class([
    'flex items-center gap-3 pt-2',
    'justify-between' => $align === 'between',
    'justify-end' => $align === 'end',
]) }}>
    {{ $slot }}
</div>
