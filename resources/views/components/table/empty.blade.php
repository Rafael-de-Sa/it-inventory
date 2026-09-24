@props(['colspan'])

<tr>
    <td colspan="{{ $colspan }}" {{ $attributes->class('px-4 py-6 text-center text-gray-300') }}>
        {{ $slot }}
    </td>
</tr>
