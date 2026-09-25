{{--
    Campos de registro (abertura) e edição de ocorrência. Variáveis: $ocorrencia (ou null), $equipamentoId, $equipamentos,
    $funcionarios, $responsaveis (equipamento_id => quem está com ele), $problemasAnteriores, $canais.
    O encerramento (liberação, solução e custos) é feito pela ação "Encerrar ocorrência" (partials/encerrar).
    Busca nos selects (data-combobox), máscaras e quem está com o equipamento: resources/js/ocorrencias/ocorrencia-form.js
--}}
@php
    $data = fn (string $campo, $padrao = null) => $ocorrencia?->{$campo}?->format('Y-m-d') ?? $padrao;
    $moeda = fn ($valor) => filled($valor) ? number_format((float) $valor, 2, ',', '.') : null;
    $resolvida = $ocorrencia && ! $ocorrencia->estaAberta();
@endphp

<x-form.fieldset legend="Equipamento">
    @if ($ocorrencia)
        <x-form.readonly label="Equipamento" :value="$equipamentos[$ocorrencia->equipamento_id] ?? '#' . $ocorrencia->equipamento_id" />
    @else
        <x-form.select name="equipamento_id" label="Equipamento" required placeholder="Digite o modelo, a identificação ou o nº de série…"
            :options="$equipamentos" :value="$equipamentoId" data-combobox data-responsaveis="{{ $responsaveis->toJson() }}"
            help="O equipamento passa para “Em manutenção” até o encerramento da ocorrência." />

        {{-- Preenchido pelo JS quando o equipamento está com um funcionário. --}}
        <div data-responsavel hidden class="space-y-2 rounded-lg border border-line bg-surface p-3">
            <p class="text-sm text-ink">
                <i class="fa-solid fa-user-tie mr-1 text-ink-subtle" aria-hidden="true"></i>
                Com <strong data-responsavel-nome></strong> — termo #<span data-responsavel-termo></span>
            </p>
            <x-form.checkbox name="recolher" label="Recolher o equipamento para manutenção" :checked="true" />
            <p class="pl-6 text-xs text-ink-muted">
                Registra a devolução (motivo Manutenção) e gera o termo de devolução para assinatura.
                Desmarque se o problema for resolvido com o equipamento ainda com o funcionário, ou se ele for
                substituído na hora (use “Registrar troca” na ocorrência).
            </p>
        </div>
    @endif

    <x-form.select name="funcionario_id" label="Último usuário" placeholder="Digite o nome ou a matrícula…"
        :options="$funcionarios" :value="$ocorrencia?->funcionario_id" data-combobox
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
            :value="$ocorrencia?->canal" placeholder="GLPI, PagBank, Cielo…" wrapper-class="md:col-span-6" />
        <x-form.input name="protocolo" label="Protocolo / nº do chamado" maxlength="50"
            :value="$ocorrencia?->protocolo" wrapper-class="md:col-span-6" />
    </x-form.grid>
    <datalist id="canais_chamado">
        @foreach ($canais as $canal)
            <option value="{{ $canal }}"></option>
        @endforeach
    </datalist>
</x-form.fieldset>

@if ($resolvida)
    <x-form.fieldset legend="Liberação">
        <x-form.grid>
            <x-form.input name="liberado_em" type="date" label="Liberado pela TI em" required :value="$data('liberado_em')"
                wrapper-class="md:col-span-4" help="Para reabrir a ocorrência, use “Reabrir”." />
            <x-form.textarea name="solucao" label="Solução" required rows="2" :value="$ocorrencia->solucao"
                wrapper-class="md:col-span-8" />
        </x-form.grid>
    </x-form.fieldset>
@endif

@if ($ocorrencia)
    <x-form.fieldset legend="Custos">
        <x-form.grid>
            <x-form.input name="custo_manutencao" label="Custo da manutenção (R$)" inputmode="numeric" placeholder="0,00"
                data-mascara="moeda" :value="$moeda($ocorrencia->custo_manutencao)" wrapper-class="md:col-span-4" />
            <x-form.input name="fornecedor" label="Fornecedor / assistência técnica" maxlength="80"
                :value="$ocorrencia->fornecedor" wrapper-class="md:col-span-4" />
            <x-form.input name="valor_cobrado" label="Valor cobrado do colaborador (R$)" inputmode="numeric" placeholder="0,00"
                data-mascara="moeda" :value="$moeda($ocorrencia->valor_cobrado)" wrapper-class="md:col-span-4" />
        </x-form.grid>
    </x-form.fieldset>
@endif

<x-form.textarea name="observacao" label="Observação (opcional)" rows="2" :value="$ocorrencia?->observacao" />
