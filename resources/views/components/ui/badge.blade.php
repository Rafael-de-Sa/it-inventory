{{-- Etiqueta de status. Tons: neutral | success | warning | danger | info --}}
@props(['tone' => 'neutral'])

<span {{ $attributes->class([
    'inline-flex items-center rounded-full border px-2 py-0.5 text-xs font-medium',
    'border-gray-500/60 bg-gray-500/20 text-gray-200' => $tone === 'neutral',
    'border-green-500/60 bg-green-500/20 text-green-200' => $tone === 'success',
    'border-yellow-500/60 bg-yellow-500/20 text-yellow-200' => $tone === 'warning',
    'border-red-500/60 bg-red-500/20 text-red-200' => $tone === 'danger',
    'border-blue-500/60 bg-blue-500/20 text-blue-200' => $tone === 'info',
]) }}>{{ $slot }}</span>
