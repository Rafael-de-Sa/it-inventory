{{--
    Campos de registro/edição de ocorrência. Variáveis: $ocorrencia (ou null), $equipamentoId, $equipamentos,
    $funcionarios, $responsaveis (equipamento_id => funcionario_id), $problemasAnteriores, $canais.
    Máscara do valor e sugestão do último usuário: resources/js/ocorrencias/ocorrencia-form.js
--}}
@php
    $data = fn (string $campo, $padrao = null) => $ocorrencia?->{$campo}?->format('Y-m-d') ?? $padrao;
    $valorCobrado = filled($ocorrencia?->valor_cobrado) ? number_format((float) $ocorrencia->valor_cobrado, 2, ',', '.') : null;
@endphp

<x-form.fieldset legend="Equipamento">
    @if ($ocorrencia)
        <x-form.readonly label="Equipamento" :value="$equipamentos[$ocorrencia->equipamento_id] ?? '#' . $ocorrencia->equipamento_id" />
    @else
        <x-form.select name="equipamento_id" label="Equipamento" required placeholder="Selecione…"
            :options="$equipamentos" :value="$equipamentoId" data-responsaveis="{{ $responsaveis->toJson() }}"
            help="Se o equipamento estiver com a TI (disponível), passa para “Em manutenção” até a liberação." />
    @endif

    <x-form.select name="funcionario_id" label="Último usuário" placeholder="Não informado"
        :options="$funcionarios" :value="$ocorrencia?->funcionario_id"
        help="Quem usava o equipamento quando o problema aconteceu. Em branco, vale quem está com ele." />
</x-form.fieldset>

<x-form.fieldset legend="Problema">
    <x-form.grid>
        <x-form.input name="reportado_em" type="date" label="Reportado em" required
            :value="$data('reportado_em', today()->format('Y-m-d'))" wrapper-class="md:col-span-4" />
        <x-form.input name="data_problema" type="date" label="Data do problema"
            :value="$data('data_problema')" wrapper-class="md:col-span-4" />
        <x-form.input name="previsao_em" type="date" label="Previsão"
            :value="$data('previsao_em')" wrapper-class="md:col-span-4" />
    </x-form.grid>

    <x-form.input name="problema" label="Erro / problema" required maxlength="255" list="problemas_anteriores"
        :value="$ocorrencia?->problema" placeholder="Ex.: Erro SecStatus 0x2014"
        help="Sugere problemas já registrados, para manter o mesmo texto." />
    <datalist id="problemas_anteriores">
        @foreach ($problemasAnteriores as $problema)
            <option value="{{ $problema }}"></option>
        @endforeach
    </datalist>

    <x-form.grid>
        <x-form.input name="canal" label="Canal do chamado" maxlength="30" list="canais_chamado"
            :value="$ocorrencia?->canal" placeholder="GLPI, PagBank, Cielo…" wrapper-class="md:col-span-4" />
        <x-form.input name="protocolo" label="Protocolo / nº do chamado" maxlength="50"
            :value="$ocorrencia?->protocolo" wrapper-class="md:col-span-4" />
        <x-form.input name="valor_cobrado" label="Valor cobrado do colaborador (R$)" inputmode="numeric"
            placeholder="0,00" data-mascara="moeda" :value="$valorCobrado" wrapper-class="md:col-span-4"
            help="Aparece para o DP no relatório do funcionário." />
    </x-form.grid>
    <datalist id="canais_chamado">
        @foreach ($canais as $canal)
            <option value="{{ $canal }}"></option>
        @endforeach
    </datalist>
</x-form.fieldset>

<x-form.fieldset legend="Liberação">
    <x-form.grid>
        <x-form.input name="liberado_em" type="date" label="Liberado pela TI em" :value="$data('liberado_em')"
            wrapper-class="md:col-span-4" help="Preenchida, a ocorrência fica resolvida." />
        <x-form.textarea name="solucao" label="Solução" rows="2" :value="$ocorrencia?->solucao"
            placeholder="Ex.: limpeza do app, troca do equipamento…" wrapper-class="md:col-span-8" />
    </x-form.grid>
</x-form.fieldset>

<x-form.textarea name="observacao" label="Observação (opcional)" rows="2" :value="$ocorrencia?->observacao" />
