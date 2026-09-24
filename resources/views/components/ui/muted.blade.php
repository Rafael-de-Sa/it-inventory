{{-- Texto discreto para valores ausentes em tabelas (ex.: "Nunca acessou"). --}}
<span {{ $attributes->class('text-xs italic text-ink-subtle') }}>{{ $slot }}</span>
