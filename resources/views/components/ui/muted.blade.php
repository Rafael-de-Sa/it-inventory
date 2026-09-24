{{-- Texto discreto para valores ausentes em tabelas (ex.: "Nunca acessou"). --}}
<span {{ $attributes->class('text-xs italic text-gray-400') }}>{{ $slot }}</span>
