{{--
    Checkbox "Ativo" com badge de status. Envia 0 quando desmarcado.
    Use `disabled` nas telas de visualização.
--}}
@props([
    'name' => 'ativo',
    'label' => 'Ativo',
    'checked' => false,
    'disabled' => false,
    'activeLabel' => 'Ativo',
    'inactiveLabel' => 'Inativo',
    'id' => null,
    'wrapperClass' => null,
])

@php
    $id ??= $name;
    $isChecked = (bool) ($disabled ? $checked : old($name, $checked));
@endphp

<x-form.field :label="$label" :for="$id" :error-key="$name" :class="$wrapperClass">
    <div class="flex h-10 items-center gap-3">
        @unless ($disabled)
            <input type="hidden" name="{{ $name }}" value="0">
        @endunless

        <input id="{{ $id }}" type="checkbox" value="1" @unless ($disabled) name="{{ $name }}" @endunless
            @checked($isChecked) @disabled($disabled)
            {{ $attributes->class([
                'h-4 w-4 cursor-pointer',
                'cursor-default opacity-60' => $disabled,
            ]) }}>

        <x-ui.status-badge :active="$isChecked" :active-label="$activeLabel" :inactive-label="$inactiveLabel" />
    </div>
</x-form.field>
