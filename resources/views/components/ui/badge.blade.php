{{-- Etiqueta de status. Tons: neutral | success | warning | danger | info --}}
@props(['tone' => 'neutral'])

<span {{ $attributes->class([
    'inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium whitespace-nowrap ring-1 ring-inset',
    'bg-zinc-100 text-zinc-700 ring-zinc-500/20' => $tone === 'neutral',
    'bg-brand-50 text-brand-800 ring-brand-600/25' => $tone === 'success',
    'bg-amber-50 text-amber-800 ring-amber-600/25' => $tone === 'warning',
    'bg-red-50 text-red-700 ring-red-600/20' => $tone === 'danger',
    'bg-sky-50 text-sky-800 ring-sky-600/20' => $tone === 'info',
]) }}>{{ $slot }}</span>
