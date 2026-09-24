{{--
    <x-form.input name="nome" label="Nome" :value="$model->nome" required help="..." />
    Atributos extras (maxlength, placeholder, data-*…) vão para o <input>.
    `wrapper-class` vai para o invólucro (ex.: md:col-span-4).
    `mask` (cpf | cnpj | cep | telefone) formata o valor com App\Support\Mask,
    inclusive o old() que volta só com dígitos após erro de validação.
--}}
@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'mask' => null,
    'id' => null,
    'help' => null,
    'required' => false,
    'wrapperClass' => null,
])

@php
    $errorKey = rtrim(str_replace(['][', '[', ']'], ['.', '.', ''], $name), '.');
    $id ??= str_replace('.', '_', $errorKey);
    $invalid = $errors->has($errorKey);
    $value = $type === 'password' ? null : old($errorKey, $value);

    if ($mask) {
        $value = \App\Support\Mask::{$mask}($value);
    }
@endphp

<x-form.field :label="$label" :for="$id" :error-key="$errorKey" :help="$help" :required="$required" :class="$wrapperClass">
    <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" value="{{ $value }}" @required($required)
        aria-invalid="{{ $invalid ? 'true' : 'false' }}" aria-describedby="{{ $id }}_help"
        {{ $attributes->class(['form-control', 'form-control-invalid' => $invalid]) }}>
</x-form.field>
