@extends('relatorios.layouts.base')

@section('titulo_pagina', 'Histórico de Movimentações do Equipamento')

@section('content')
    @php
        use Illuminate\Support\Str;

        $nomeTipoEquipamento = $equipamento->tipoEquipamento->nome ?? null;
        $descricaoEquipamento = $equipamento->descricao ?? null;
        $patrimonioEquipamento = $equipamento->patrimonio ?? null;
        $numeroSerieEquipamento = $equipamento->numero_serie ?? null;

        $tituloEquipamento = trim(
            ($nomeTipoEquipamento ? $nomeTipoEquipamento . ' ' : '') . ($descricaoEquipamento ?? ''),
        );

        $tituloEquipamentoMaiusculo = Str::upper($tituloEquipamento);
    @endphp

    <table class="cabecalho-geral">
        <tr>
            <td class="cabecalho-col-sistema">
                <div class="cabecalho-sistema">
                    <img src="{{ public_path('assets/logo-teste.png') }}" alt="Logo IT Inventory"
                        class="cabecalho-sistema-logo">

                    <div>
                        <div class="cabecalho-sistema-nome">
                            IT INVENTORY
                        </div>
                        <div style="font-size: 10px; color: #4b5563;">
                            Sistema de Gestão de Ativos de TI
                        </div>
                    </div>
                </div>
            </td>

            <td class="cabecalho-col-empresa">
                <div class="cabecalho-empresa">
                    <h2 class="cabecalho-empresa-nome">
                        @if (!empty($tituloEquipamentoMaiusculo))
                            {{ $tituloEquipamentoMaiusculo }}
                        @elseif (!empty($nomeTipoEquipamento))
                            {{ Str::upper($nomeTipoEquipamento) }}
                        @else
                            EQUIPAMENTO ID {{ $equipamento->id }}
                        @endif
                    </h2>

                    @if (!empty($patrimonioEquipamento))
                        <span class="cabecalho-empresa-linha-secundaria">
                            Patrimônio: {{ $patrimonioEquipamento }}
                        </span><br>
                    @endif

                    @if (!empty($numeroSerieEquipamento))
                        <span class="cabecalho-empresa-linha-secundaria">
                            Número de série: {{ $numeroSerieEquipamento }}
                        </span><br>
                    @endif

                    @if (empty($patrimonioEquipamento) && empty($numeroSerieEquipamento) && empty($tituloEquipamentoMaiusculo))
                        <span class="cabecalho-empresa-linha-secundaria">
                            Identificador interno: {{ $equipamento->id }}
                        </span><br>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="titulo-principal">
        HISTÓRICO DE MOVIMENTAÇÕES DO EQUIPAMENTO
    </div>

    <div class="secao-texto">
        <p>
            @if (!empty($nomeTipoEquipamento))
                <strong>Tipo:</strong> {{ $nomeTipoEquipamento }}<br>
            @endif

            @if (!empty($descricaoEquipamento))
                <strong>Descrição / Modelo:</strong> {{ $descricaoEquipamento }}<br>
            @endif

            @if (!empty($patrimonioEquipamento))
                <strong>Patrimônio:</strong> {{ $patrimonioEquipamento }}<br>
            @endif

            @if (!empty($numeroSerieEquipamento))
                <strong>Número de série:</strong> {{ $numeroSerieEquipamento }}<br>
            @endif
        </p>
    </div>

    <div class="secao-texto">
        <h2 class="subtitulo-secao">
            Histórico de termos de responsabilidade
        </h2>

        @if ($listaMovimentacoesResponsabilidade->isEmpty())
            <p>Não há movimentações de responsabilidade registradas para este equipamento.</p>
        @else
            <table class="tabela-equipamentos">
                <thead>
                    <tr>
                        <th>Mov.</th>
                        <th>Data empréstimo</th>
                        <th>Data devolução</th>
                        <th class="texto-esquerda">Motivo devolução</th>
                        <th class="texto-esquerda">Funcionário</th>
                        <th class="texto-esquerda">Observação da devolução</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($listaMovimentacoesResponsabilidade as $registroPivot)
                        @php
                            $movimentacao = $registroPivot->movimentacao;
                            $funcionario = $movimentacao?->funcionario;

                            $nomeCompletoFuncionario = $funcionario
                                ? trim(($funcionario->nome ?? '') . ' ' . ($funcionario->sobrenome ?? ''))
                                : null;

                            $foiDevolvido = !is_null($registroPivot->devolvido_em);

                            $motivoBruto = $registroPivot->motivo_devolucao;

                            $motivoPadronizado = match ($motivoBruto) {
                                'manutencao' => 'Manutenção',
                                'defeito' => 'Defeito',
                                'quebra' => 'Quebra',
                                'devolucao' => 'Devolução',
                                'cancelada' => 'Movimentação cancelada',
                                default => null,
                            };
                        @endphp

                        <tr>
                            <td>
                                @if ($movimentacao)
                                    #{{ $movimentacao->id }}
                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                {{ $movimentacao?->criado_em?->format('d/m/Y') ?? '-' }}
                            </td>

                            <td>
                                {{ $registroPivot->devolvido_em?->format('d/m/Y') ?? '-' }}
                            </td>

                            <td class="texto-esquerda">
                                @if ($foiDevolvido && !empty($motivoPadronizado))
                                    {{ $motivoPadronizado }}
                                @else
                                    -
                                @endif
                            </td>

                            <td class="texto-esquerda">
                                @if ($funcionario)
                                    #{{ $funcionario->id }}
                                    {{ $nomeCompletoFuncionario }}

                                    @if (!empty($funcionario->matricula))
                                        (Matrícula {{ $funcionario->matricula }})
                                    @endif
                                @else
                                    -
                                @endif
                            </td>

                            <td class="texto-esquerda">
                                @if ($foiDevolvido && !empty($registroPivot->observacao))
                                    {{ $registroPivot->observacao }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="rodape-emissao">
        Relatório gerado em {{ $dataHoraEmissao->format('d/m/Y H:i') }}
    </div>
@endsection
