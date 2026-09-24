{{-- Campo de upload de arquivo com label, ajuda e erro: <x-form.file name="arquivo_termo" accept="application/pdf" /> --}}
@props([
    'name',
    'label' => null,
    'id' => null,
    'help' => null,
    'required' => false,
    'wrapperClass' => null,
])

@php $id ??= $name; @endphp

<x-form.field :label="$label" :for="$id" :error-key="$name" :help="$help" :required="$required" :class="$wrapperClass">
    <input id="{{ $id }}" name="{{ $name }}" type="file" @required($required) aria-describedby="{{ $id }}_help"
        {{ $attributes->class(
            'block w-full cursor-pointer text-sm text-green-50 file:mr-3 file:cursor-pointer file:rounded-lg file:border-0 file:bg-green-700 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-green-600'
        ) }}>
</x-form.field>
