@extends('relatorios.layouts.base')

@section('titulo_pagina', 'Relatório de Ocorrência')

@section('content')
    @php
        use App\Models\Ocorrencia;
        use App\Services\IndicadoresManutencao;
        use Illuminate\Support\Str;

        $data = fn ($valor) => $valor?->format('d/m/Y') ?? '-';
        $titulo = Str::upper(trim(($equipamento->tipoEquipamento?->nome ?? '') . ' ' . $equipamento->nome_exibicao));
        $dias = $ocorrencia->liberado_em ? $ocorrencia->reportado_em->diffInDays($ocorrencia->liberado_em) : null;
    @endphp

    <table class="cabecalho-geral">
        <tr>
            <td class="cabecalho-col-sistema">
                <div class="cabecalho-sistema">
                    <img src="{{ public_path('assets/logo-teste.png') }}" alt="Logo IT Inventory" class="cabecalho-sistema-logo">
                    <div>
                        <div class="cabecalho-sistema-nome">IT INVENTORY</div>
                        <div style="font-size: 10px; color: #4b5563;">Sistema de Gestão de Ativos de TI</div>
                    </div>
                </div>
            </td>
            <td class="cabecalho-col-empresa">
                <div class="cabecalho-empresa">
                    <h2 class="cabecalho-empresa-nome">{{ $titulo ?: 'EQUIPAMENTO ID ' . $equipamento->id }}</h2>
                    @if ($equipamento->identificacao)
                        <span class="cabecalho-empresa-linha-secundaria">Identificação: {{ $equipamento->identificacao }}</span><br>
                    @endif
                    @if ($equipamento->patrimonio)
                        <span class="cabecalho-empresa-linha-secundaria">Patrimônio: {{ $equipamento->patrimonio }}</span><br>
                    @endif
                    @if ($equipamento->numero_serie)
                        <span class="cabecalho-empresa-linha-secundaria">Número de série: {{ $equipamento->numero_serie }}</span><br>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="titulo-principal">RELATÓRIO DE OCORRÊNCIA Nº {{ $ocorrencia->id }}</div>

    <div class="secao-texto">
        <p>
            <strong>Situação:</strong> {{ $ocorrencia->situacao_rotulo }}<br>
            <strong>Equipamento:</strong> #{{ $equipamento->id }} {{ $equipamento->tipoEquipamento?->nome }} {{ $equipamento->nome_exibicao }}
            ({{ $equipamento->status_rotulo }})<br>
            @if ($equipamento->resumo_tecnico)
                <strong>Configuração:</strong> {{ $equipamento->resumo_tecnico }}<br>
            @endif
            <strong>Último usuário:</strong>
            {{ $ocorrencia->funcionario ? $ocorrencia->funcionario->nome_completo . ($ocorrencia->funcionario->matricula ? ' (matrícula ' . $ocorrencia->funcionario->matricula . ')' : '') : '-' }}<br>
            <strong>Registrada por:</strong>
            {{ $ocorrencia->usuario?->funcionario?->nome_completo ?? ($ocorrencia->usuario?->email ?? '-') }}
            em {{ $ocorrencia->criado_em?->format('d/m/Y H:i') }}
        </p>
    </div>

    <h2 class="subtitulo-secao">Problema</h2>
    <table class="tabela-ficha">
        <tr><td class="rotulo-ficha">Erro / problema</td><td>{{ $ocorrencia->problema }}</td></tr>
        <tr><td class="rotulo-ficha">Data do problema</td><td>{{ $data($ocorrencia->data_problema) }}</td></tr>
        <tr><td class="rotulo-ficha">Reportado em</td><td>{{ $data($ocorrencia->reportado_em) }}</td></tr>
        <tr><td class="rotulo-ficha">Previsão</td><td>{{ $data($ocorrencia->previsao_em) }}</td></tr>
        <tr><td class="rotulo-ficha">Chamado</td><td>{{ $ocorrencia->chamado ?? '-' }}</td></tr>
    </table>

    <h2 class="subtitulo-secao">Manutenção</h2>
    <table class="tabela-ficha">
        <tr><td class="rotulo-ficha">Liberado pela TI em</td><td>{{ $data($ocorrencia->liberado_em) }}{{ $dias !== null ? " ({$dias} dia(s) após o registro)" : '' }}</td></tr>
        <tr><td class="rotulo-ficha">Solução</td><td>{{ $ocorrencia->solucao ?? '-' }}</td></tr>
        @if ($ocorrencia->troca)
            <tr><td class="rotulo-ficha">Troca</td><td>Termo de troca #{{ $ocorrencia->troca->id }} ({{ Str::lower($ocorrencia->troca->tipo_troca_rotulo) }})</td></tr>
        @endif
        <tr><td class="rotulo-ficha">Fornecedor / assistência</td><td>{{ $ocorrencia->fornecedor ?? '-' }}</td></tr>
        <tr><td class="rotulo-ficha">Custo da manutenção</td><td>{{ $ocorrencia->custo_manutencao_formatado ?? '-' }}</td></tr>
        <tr><td class="rotulo-ficha">Valor cobrado do colaborador</td><td>{{ $ocorrencia->valor_cobrado_formatado ?? '-' }}</td></tr>
    </table>

    @if ($ocorrencia->observacao)
        <div class="secao-texto"><strong>Observação:</strong> {{ $ocorrencia->observacao }}</div>
    @endif

    <h2 class="subtitulo-secao">Manutenção acumulada do equipamento</h2>
    <table class="tabela-equipamentos">
        <thead>
            <tr>
                <th>Custo total</th>
                <th>% do valor de compra</th>
                <th>Ocorrências</th>
                <th>Tempo parado</th>
                <th>Disponibilidade</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ Ocorrencia::reais($indicadores['custo']) }}</td>
                <td>{{ IndicadoresManutencao::percentual($indicadores['percentual_custo']) }}</td>
                <td>{{ $indicadores['ocorrencias'] }}</td>
                <td>{{ IndicadoresManutencao::duracao($indicadores['segundos_parado']) }}</td>
                <td>{{ IndicadoresManutencao::percentual($indicadores['disponibilidade']) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="rodape-emissao">
        Relatório gerado em {{ $dataHoraEmissao->format('d/m/Y H:i') }}
    </div>
@endsection
