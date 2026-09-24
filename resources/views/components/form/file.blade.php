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
            'block w-full cursor-pointer rounded-lg border border-line-strong bg-surface text-sm text-ink-muted file:mr-3 file:cursor-pointer file:border-0 file:border-r file:border-line file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-brand-800 hover:file:bg-brand-100'
        ) }}>
</x-form.field>
