@extends('relatorios.layouts.base')

@section('titulo_pagina', 'Relatório de Equipamentos por Funcionário')

@section('content')
    @php
        use App\Support\Mask;
        use Illuminate\Support\Str;

        $empresaFuncionario = $funcionario->setor->empresa ?? null;
        $setorFuncionario = $funcionario->setor ?? null;

        $razaoSocialEmpresa = $empresaFuncionario->razao_social ?? null;
        $nomeFantasiaEmpresa = $empresaFuncionario->nome_fantasia ?? null;

        $cnpjFormatado = !empty($empresaFuncionario?->cnpj) ? Mask::cnpj($empresaFuncionario->cnpj) : null;

        $rua = $empresaFuncionario->rua ?? null;
        $numero = $empresaFuncionario->numero ?? null;
        $bairro = $empresaFuncionario->bairro ?? null;
        $cidade = $empresaFuncionario->cidade ?? null;
        $estado = $empresaFuncionario->estado ?? null;

        $partesEndereco = [];

        if (!empty($rua)) {
            $partesEndereco[] = $rua . (!empty($numero) ? ', ' . $numero : '');
        } elseif (!empty($numero)) {
            $partesEndereco[] = 'Nº ' . $numero;
        }

        if (!empty($bairro)) {
            $partesEndereco[] = $bairro;
        }

        if (!empty($cidade) && !empty($estado)) {
            $partesEndereco[] = "{$cidade}/{$estado}";
        } elseif (!empty($cidade)) {
            $partesEndereco[] = $cidade;
        }

        $enderecoFormatado = implode(' – ', $partesEndereco);

        $nomeCompletoFuncionario = trim(($funcionario->nome ?? '') . ' ' . ($funcionario->sobrenome ?? ''));
        $nomeCompletoFuncionarioMaiusculo = Str::upper($nomeCompletoFuncionario);
        $cpfFormatadoFuncionario = !empty($funcionario->cpf) ? Mask::cpf($funcionario->cpf) : null;
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
                    @if ($razaoSocialEmpresa)
                        <strong>{{ Str::upper($razaoSocialEmpresa) }}</strong><br>
                    @endif

                    @if ($nomeFantasiaEmpresa)
                        <span class="cabecalho-empresa-linha-secundaria">
                            Nome fantasia: {{ $nomeFantasiaEmpresa }}
                        </span><br>
                    @endif

                    @if (!empty($cnpjFormatado))
                        <span class="cabecalho-empresa-linha-secundaria">
                            CNPJ: {{ $cnpjFormatado }}
                        </span><br>
                    @endif

                    @if (!empty($enderecoFormatado))
                        <span class="cabecalho-empresa-linha-secundaria">
                            Endereço: {{ $enderecoFormatado }}
                        </span><br>
                    @endif

                    @if (!empty($setorFuncionario?->nome))
                        <span class="cabecalho-empresa-linha-secundaria">
                            Setor: {{ $setorFuncionario->nome }}
                        </span><br>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="titulo-principal">
        RELATÓRIO DE EQUIPAMENTOS POR FUNCIONÁRIO
    </div>

    <div class="secao-texto">
        <p>
            <strong>Funcionário:</strong>
            {{ $nomeCompletoFuncionarioMaiusculo }}
        </p>

        @if (!empty($funcionario->matricula))
            <p>
                <strong>Matrícula:</strong>
                {{ $funcionario->matricula }}
            </p>
        @endif

        @if ($cpfFormatadoFuncionario)
            <p>
                <strong>CPF:</strong>
                {{ $cpfFormatadoFuncionario }}
            </p>
        @endif
    </div>

    <div class="secao-texto">
        <h2 class="subtitulo-secao">
            Equipamentos vinculados
        </h2>

        @if ($listaDeEquipamentosEmUso->isEmpty())
            <p>Não há equipamentos vinculados a este funcionário.</p>
        @else
            <table class="tabela-equipamentos">
                <thead>
                    <tr>
                        <th>Patrimônio</th>
                        <th>Tipo</th>
                        <th class="texto-esquerda">Descrição / Modelo</th>
                        <th>Número de série</th>
                        <th>Data da movimentação</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($listaDeEquipamentosEmUso as $registroPivot)
                        @php
                            $equipamento = $registroPivot->equipamento;
                            $movimentacao = $registroPivot->movimentacao;
                        @endphp
                        <tr>
                            <td>{{ $equipamento->patrimonio }}</td>
                            <td>{{ $equipamento->tipoEquipamento->nome ?? '' }}</td>
                            <td class="texto-esquerda">{{ $equipamento->descricao ?? '' }}</td>
                            <td>{{ $equipamento->numero_serie ?? '' }}</td>
                            <td>{{ $movimentacao->criado_em?->format('d/m/Y') ?? '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="rodape-emissao">
        Relatório gerado em {{ $dataGeracaoRelatorio->format('d/m/Y H:i') }}
    </div>
@endsection
