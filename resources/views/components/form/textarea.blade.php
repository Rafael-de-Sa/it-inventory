@props([
    'name',
    'label' => null,
    'value' => null,
    'id' => null,
    'help' => null,
    'required' => false,
    'wrapperClass' => null,
])

@php
    $errorKey = rtrim(str_replace(['][', '[', ']'], ['.', '.', ''], $name), '.');
    $id ??= str_replace('.', '_', $errorKey);
    $invalid = $errors->has($errorKey);
@endphp

<x-form.field :label="$label" :for="$id" :error-key="$errorKey" :help="$help" :required="$required" :class="$wrapperClass">
    <textarea id="{{ $id }}" name="{{ $name }}" @required($required)
        aria-invalid="{{ $invalid ? 'true' : 'false' }}" aria-describedby="{{ $id }}_help"
        {{ $attributes->merge(['rows' => 3])->class(['form-control', 'form-control-invalid' => $invalid]) }}>{{ old($errorKey, $value) }}</textarea>
</x-form.field>
