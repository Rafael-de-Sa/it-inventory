{{--
    Campo somente leitura para telas de visualização (show).
    `multiline` exibe textos longos preservando quebras de linha (ex.: descrição).
--}}
@props([
    'label' => null,
    'value' => null,
    'id' => null,
    'multiline' => false,
    'wrapperClass' => null,
])

@php $texto = filled($value) ? $value : '—'; @endphp

<x-form.field :label="$label" :for="$multiline ? null : $id" :class="$wrapperClass">
    @if ($multiline)
        <div @if ($id) id="{{ $id }}" @endif
            {{ $attributes->class('form-control form-control-readonly h-auto min-h-[3.25rem] whitespace-pre-line') }}>{{ $texto }}</div>
    @else
        <input @if ($id) id="{{ $id }}" @endif type="text" value="{{ $texto }}" disabled readonly
            {{ $attributes->class('form-control form-control-readonly') }}>
    @endif
</x-form.field>
