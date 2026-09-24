@props(['legend'])

<fieldset {{ $attributes->class('space-y-4 rounded-lg border border-line bg-surface-muted p-4 md:p-5') }}>
    <legend class="px-2 text-sm font-semibold text-ink">{{ $legend }}</legend>
    {{ $slot }}
</fieldset>
