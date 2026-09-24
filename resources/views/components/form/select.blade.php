{{--
    Opções simples (array valor => rótulo):
        <x-form.select name="ativo" :options="['1' => 'Ativo', '0' => 'Inativo']" placeholder="Todos" />

    Coleção de models:
        <x-form.select name="empresa_id" :options="$empresas" option-label="razao_social" />
        <x-form.select name="empresa_id" :options="$empresas" :option-label="fn ($e) => ..." />

    Opções customizadas também podem ir no slot.
--}}
@props([
    'name',
    'label' => null,
    'value' => null,
    'options' => [],
    'optionValue' => 'id',
    'optionLabel' => null,
    'placeholder' => null,
    'id' => null,
    'help' => null,
    'required' => false,
    'wrapperClass' => null,
])

@php
    $errorKey = rtrim(str_replace(['][', '[', ']'], ['.', '.', ''], $name), '.');
    $id ??= str_replace('.', '_', $errorKey);
    $invalid = $errors->has($errorKey);
    $selected = (string) old($errorKey, $value);

    $items = collect($options)->map(fn ($item, $key) => $optionLabel === null
        ? ['value' => $key, 'label' => $item]
        : [
            'value' => data_get($item, $optionValue),
            'label' => $optionLabel instanceof Closure ? $optionLabel($item) : data_get($item, $optionLabel),
        ]);
@endphp

<x-form.field :label="$label" :for="$id" :error-key="$errorKey" :help="$help" :required="$required" :class="$wrapperClass">
    <select id="{{ $id }}" name="{{ $name }}" @required($required)
        aria-invalid="{{ $invalid ? 'true' : 'false' }}" aria-describedby="{{ $id }}_help"
        {{ $attributes->class(['form-control', 'form-control-invalid' => $invalid]) }}>
        @if ($placeholder !== null)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($items as $item)
            <option value="{{ $item['value'] }}" @selected((string) $item['value'] === $selected)>{{ $item['label'] }}</option>
        @endforeach

        {{ $slot }}
    </select>
</x-form.field>
