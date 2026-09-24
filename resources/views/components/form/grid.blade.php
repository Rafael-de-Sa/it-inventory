{{-- Grid de 12 colunas para posicionar campos com `wrapper-class="md:col-span-N"`. --}}
<div {{ $attributes->class('grid grid-cols-1 gap-5 md:grid-cols-12') }}>
    {{ $slot }}
</div>
