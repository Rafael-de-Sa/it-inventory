{{--
    Formulário dentro de card, com @csrf, spoofing de método e resumo de erros.
    <x-form.card :action="route('x.update', $x)" method="PUT" title="Editar X">...</x-form.card>
--}}
@props([
    'action',
    'method' => 'POST',
    'title' => null,
    'subtitle' => null,
    'size' => 'md',
])

@php
    $method = strtoupper($method);
    $formMethod = $method === 'GET' ? 'GET' : 'POST';
@endphp

<x-ui.card :size="$size">
    <form action="{{ $action }}" method="{{ $formMethod }}" {{ $attributes->class('space-y-6') }}>
        @if ($formMethod === 'POST')
            @csrf
        @endif
        @unless (in_array($method, ['GET', 'POST']))
            @method($method)
        @endunless

        @if ($title)
            <x-ui.card-header :title="$title" :subtitle="$subtitle" />
        @endif

        <x-form.errors-summary />

        {{ $slot }}
    </form>
</x-ui.card>
