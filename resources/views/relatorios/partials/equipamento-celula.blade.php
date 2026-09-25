{{--
    Identificação do equipamento em termos e relatórios PDF: tipo + fabricante/modelo, identificação interna
    e resumo técnico (configuração do computador, IMEI do celular...). Variável: $equipamento
    (carregar tipoEquipamento e Equipamento::RELACOES_FICHA para evitar consultas extras).
--}}
<strong>{{ $equipamento->tipoEquipamento?->nome ? $equipamento->tipoEquipamento->nome . ' — ' : '' }}{{ $equipamento->nome_exibicao ?: 'Descrição não informada' }}</strong>
@if ($equipamento->identificacao)
    <br><span class="nota-discreta">Identificação: {{ $equipamento->identificacao }}</span>
@endif
@if ($equipamento->resumo_tecnico)
    <br><span class="nota-discreta">{{ $equipamento->resumo_tecnico }}</span>
@endif
