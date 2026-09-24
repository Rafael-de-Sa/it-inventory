@props(['colspan'])

<tr>
    <td colspan="{{ $colspan }}" {{ $attributes->class('px-4 py-10 text-center text-sm text-ink-muted') }}>
        {{ $slot }}
    </td>
</tr>
