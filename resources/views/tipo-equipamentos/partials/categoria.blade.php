{{--
    Categoria do tipo: define a ficha técnica dos equipamentos. Travada quando o tipo já tem equipamentos,
    pois as fichas cadastradas pertencem à categoria atual. Variável: $tipoEquipamento (null no cadastro).
--}}
@use('App\Enums\CategoriaEquipamento')

@php
    $possuiEquipamentos = $tipoEquipamento?->equipamentos()->withTrashed()->exists() ?? false;
    $ajuda = collect(CategoriaEquipamento::cases())
        ->map(fn ($categoria) => $categoria->label() . ': ' . $categoria->descricao())
        ->implode(' ');
@endphp

@if ($possuiEquipamentos)
    <input type="hidden" name="categoria" value="{{ $tipoEquipamento->categoria->value }}">
    <x-form.readonly id="categoria_travada" label="Categoria (ficha técnica)" :value="$tipoEquipamento->categoria->label()" />
    <p class="-mt-4 text-xs text-ink-muted">
        A categoria não pode ser alterada porque já existem equipamentos cadastrados com este tipo.
    </p>
@else
    <x-form.select name="categoria" label="Categoria (ficha técnica)" required placeholder="Selecione..."
        :options="CategoriaEquipamento::options()" :value="$tipoEquipamento?->categoria?->value"
        help="Define os campos técnicos dos equipamentos deste tipo. Use “Genérico” para itens sem ficha (PINPad, teclado...)." />
@endif
