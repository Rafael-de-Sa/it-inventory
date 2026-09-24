@extends('relatorios.layouts.base')

@section('titulo_pagina', 'Histórico de Movimentações do Equipamento')

@section('content')
    @php
        use Illuminate\Support\Str;

        $nomeTipoEquipamento = $equipamento->tipoEquipamento->nome ?? null;
        $descricaoEquipamento = $equipamento->nome_exibicao ?: null;
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
                <strong>Equipamento:</strong> {{ $descricaoEquipamento }}<br>
            @endif

            @if (!empty($patrimonioEquipamento))
                <strong>Patrimônio:</strong> {{ $patrimonioEquipamento }}<br>
            @endif

            @if (!empty($numeroSerieEquipamento))
                <strong>Número de série:</strong> {{ $numeroSerieEquipamento }}<br>
            @endif

            @if ($equipamento->identificacao)
                <strong>Identificação interna:</strong> {{ $equipamento->identificacao }}<br>
            @endif

            <strong>Situação atual:</strong> {{ $equipamento->status_rotulo }}<br>
        </p>

        @if ($equipamento->data_compra || filled($equipamento->valor_compra) || $equipamento->nota_fiscal || $equipamento->chave_acesso_nf)
            <p>
                @if ($equipamento->data_compra)
                    <strong>Data da compra:</strong> {{ $equipamento->data_compra->format('d/m/Y') }}<br>
                @endif
                @if (filled($equipamento->valor_compra))
                    <strong>Valor da compra:</strong> R$ {{ number_format((float) $equipamento->valor_compra, 2, ',', '.') }}<br>
                @endif
                @if ($equipamento->nota_fiscal)
                    <strong>Nota fiscal:</strong> {{ $equipamento->nota_fiscal }}<br>
                @endif
                @if ($equipamento->chave_acesso_nf)
                    <strong>Chave de acesso:</strong> {{ $equipamento->chave_acesso_nf_formatada }}<br>
                @endif
            </p>
        @endif

        @if ($equipamento->descricao && $equipamento->fabricante)
            <p><strong>Observação:</strong> {{ $equipamento->descricao }}</p>
        @endif
    </div>

    @include('relatorios.equipamentos.partials.ficha-tecnica')

    <div class="secao-texto">
        <h2 class="subtitulo-secao">
            Linha do tempo
        </h2>

        @if ($linhaDoTempo->isEmpty())
            <p>Não há eventos registrados para este equipamento.</p>
        @else
            <table class="tabela-equipamentos">
                <thead>
                    <tr>
                        <th>Data / hora</th>
                        <th class="texto-esquerda">Evento</th>
                        <th class="texto-esquerda">Status</th>
                        <th>Mov.</th>
                        <th class="texto-esquerda">Usuário</th>
                        <th class="texto-esquerda">Observação</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($linhaDoTempo as $evento)
                        <tr>
                            <td>{{ $evento->ocorrido_em->format('d/m/Y H:i') }}</td>
                            <td class="texto-esquerda">{{ $evento->evento_rotulo }}</td>
                            <td class="texto-esquerda">{{ $evento->transicao_status ?? '-' }}</td>
                            <td>{{ $evento->movimentacao_id ? '#' . $evento->movimentacao_id : '-' }}</td>
                            <td class="texto-esquerda">
                                {{ $evento->usuario?->funcionario?->nome_completo ?? ($evento->usuario?->email ?? '-') }}
                            </td>
                            <td class="texto-esquerda">
                                {{ $evento->observacao ?? ($evento->reconstruido ? '' : '-') }}
                                @if ($evento->reconstruido)
                                    @if ($evento->observacao)<br>@endif
                                    <span class="nota-discreta">Registro anterior ao histórico</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="secao-texto">
        <h2 class="subtitulo-secao">
            Ocorrências
        </h2>

        @if (($ocorrencias ?? collect())->isEmpty())
            <p>Não há ocorrências registradas para este equipamento.</p>
        @else
            <table class="tabela-equipamentos">
                <thead>
                    <tr>
                        <th>Nº</th>
                        <th>Reportado</th>
                        <th class="texto-esquerda">Problema</th>
                        <th class="texto-esquerda">Último usuário</th>
                        <th>Chamado</th>
                        <th>Liberado</th>
                        <th class="texto-esquerda">Solução</th>
                        <th>Valor cobrado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ocorrencias as $ocorrencia)
                        <tr>
                            <td>#{{ $ocorrencia->id }}</td>
                            <td>{{ $ocorrencia->reportado_em->format('d/m/Y') }}</td>
                            <td class="texto-esquerda">{{ $ocorrencia->problema }}</td>
                            <td class="texto-esquerda">{{ $ocorrencia->funcionario?->nome_completo ?? '-' }}</td>
                            <td>{{ $ocorrencia->chamado ?? '-' }}</td>
                            <td>{{ $ocorrencia->liberado_em?->format('d/m/Y') ?? 'Aberta' }}</td>
                            <td class="texto-esquerda">
                                {{ $ocorrencia->solucao ?? '-' }}
                                @if ($ocorrencia->troca_movimentacao_id)
                                    <br><span class="nota-discreta">Troca #{{ $ocorrencia->troca_movimentacao_id }}</span>
                                @endif
                            </td>
                            <td>{{ $ocorrencia->valor_cobrado_formatado ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="secao-texto">
        <h2 class="subtitulo-secao">
            Histórico de termos de responsabilidade e troca
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
                        <th>Situação</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($listaMovimentacoesResponsabilidade as $registroPivot)
                        @php
                            $movimentacao = $registroPivot->movimentacao;
                            $funcionario = $movimentacao?->funcionario;
                            $nomeCompletoFuncionario = $funcionario?->nome_completo;
                            $foiDevolvido = !is_null($registroPivot->devolvido_em);
                            $motivoPadronizado = $registroPivot->motivo_devolucao_rotulo;

                            $situacao = match (true) {
                                !$movimentacao => '-',
                                $movimentacao->trashed() => 'Excluída',
                                default => $movimentacao->status_rotulo,
                            };
                        @endphp

                        <tr>
                            <td>
                                @if ($movimentacao)
                                    #{{ $movimentacao->id }}
                                    @if ($movimentacao->tipo_movimentacao === \App\Models\Movimentacao::TIPO_TROCA)
                                        <br><span class="nota-discreta">Troca</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                {{ $movimentacao?->criado_em?->format('d/m/Y') ?? '-' }}
                            </td>

                            <td>
                                {{ $registroPivot->devolvido_em?->format('d/m/Y') ?? 'Em uso' }}
                                @if ($registroPivot->devolucao_movimentacao_id)
                                    <br><span style="font-size: 9px; color: #4b5563;">Termo #{{ $registroPivot->devolucao_movimentacao_id }}</span>
                                @endif
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

                            <td>{{ $situacao }}</td>
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
