{{--
    Dados principais de uma movimentação (responsabilidade ou devolução).
    Variáveis: $movimentacao, $rotuloObservacao (opcional).
--}}
@php
    $funcionario = $movimentacao->funcionario;
    $descricaoFuncionario = $funcionario
        ? $funcionario->nome_completo
            . ($funcionario->matricula ? " ({$funcionario->matricula})" : '')
            . ($funcionario->terceirizado ? ' - Terceirizado' : '')
        : null;
@endphp

<x-form.grid>
    <x-form.readonly label="ID da Movimentação" :value="$movimentacao->id" wrapper-class="md:col-span-3" />
    <x-form.readonly label="Data da Movimentação" :value="$movimentacao->criado_em?->format('d/m/Y H:i')"
        wrapper-class="md:col-span-3" />
    <x-form.field label="Status" class="md:col-span-3">
        <div class="flex h-10.5 items-center"><x-movimentacao.status :status="$movimentacao->status" /></div>
    </x-form.field>
    <x-form.field label="Tipo de termo" class="md:col-span-3">
        <div class="flex h-10.5 items-center"><x-ui.badge tone="info">{{ $movimentacao->tipo_rotulo }}</x-ui.badge></div>
    </x-form.field>
</x-form.grid>

<x-form.readonly label="Funcionário" :value="$descricaoFuncionario" />
<x-form.readonly label="Setor" :value="$movimentacao->setor?->nome" />
<x-form.readonly label="Empresa" :value="$movimentacao->setor?->empresa?->razao_social" />
<x-form.readonly :label="$rotuloObservacao ?? 'Observação'"
    :value="$movimentacao->observacao ?? 'Sem observações registradas.'" multiline />
