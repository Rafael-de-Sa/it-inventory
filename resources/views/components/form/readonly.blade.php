{{-- Campo somente leitura para telas de visualização (show). --}}
@props([
    'label' => null,
    'value' => null,
    'id' => null,
    'wrapperClass' => null,
])

<x-form.field :label="$label" :for="$id" :class="$wrapperClass">
    <input @if ($id) id="{{ $id }}" @endif type="text" value="{{ filled($value) ? $value : '—' }}" disabled readonly
        {{ $attributes->class('form-control form-control-readonly') }}>
</x-form.field>
