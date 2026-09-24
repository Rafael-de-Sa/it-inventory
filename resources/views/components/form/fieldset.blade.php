@props(['legend'])

<fieldset {{ $attributes->class('space-y-4 rounded-xl border border-green-800 bg-green-900/60 p-4 md:p-5') }}>
    <legend class="px-2 text-sm font-semibold tracking-wide text-green-200">{{ $legend }}</legend>
    {{ $slot }}
</fieldset>
