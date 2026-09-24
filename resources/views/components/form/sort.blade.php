{{--
    Selects "Ordenar por" + "Direção" usados nos filtros das listagens.
    <x-form.sort :options="['id' => 'ID', 'nome' => 'Nome']" :default="$ordenarPor" :default-direction="$direcao" />
--}}
@props([
    'options',
    'default' => 'id',
    'defaultDirection' => 'asc',
])

<x-form.select name="ordenar_por" label="Ordenar por" :options="$options"
    :value="request('ordenar_por', $default)" wrapper-class="md:col-span-3" />

<x-form.select name="direcao" label="Direção" :options="['asc' => 'Ascendente', 'desc' => 'Descendente']"
    :value="request('direcao', $defaultDirection)" wrapper-class="md:col-span-3" />
