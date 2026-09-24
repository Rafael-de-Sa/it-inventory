@extends('relatorios.layouts.base')

@section('titulo_pagina', 'Termo de Troca de Equipamento')

@section('content')
    @php
        use App\Support\Mask;
        use Illuminate\Support\Str;

        $funcionario = $movimentacao->funcionario;
        $setorFuncionario = $funcionario->setor ?? $movimentacao->setor;
        $empresaFuncionario = $setorFuncionario?->empresa;

        $cnpjFormatado = !empty($empresaFuncionario?->cnpj) ? Mask::cnpj($empresaFuncionario->cnpj) : null;
        $cpfFormatado = !empty($funcionario->cpf) ? Mask::cpf($funcionario->cpf) : null;
        $enderecoFormatado = collect([
            $empresaFuncionario?->logradouro,
            $empresaFuncionario?->numero ? "nº {$empresaFuncionario->numero}" : null,
            $empresaFuncionario?->bairro,
            collect([$empresaFuncionario?->cidade, $empresaFuncionario?->estado])->filter()->implode('/'),
        ])->filter()->implode(' – ');

        $nomeCompletoMaiusculo = Str::upper($funcionario->nome_completo);
        $identificacaoPessoa = $funcionario->terceirizado ? 'colaborador(a) terceirizado(a)' : 'funcionário(a)';
        $dataHoraEmissao = $movimentacao->criado_em?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i');
        $valor = fn ($equipamento) => filled($equipamento->valor_compra) ? number_format((float) $equipamento->valor_compra, 2, ',', '.') : '';
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
                    @if ($empresaFuncionario?->razao_social)
                        <strong>{{ Str::upper($empresaFuncionario->razao_social) }}</strong><br>
                    @endif
                    @if ($empresaFuncionario?->nome_fantasia)
                        <span class="cabecalho-empresa-linha-secundaria">Nome fantasia: {{ $empresaFuncionario->nome_fantasia }}</span><br>
                    @endif
                    @if ($cnpjFormatado)
                        <span class="cabecalho-empresa-linha-secundaria">CNPJ: {{ $cnpjFormatado }}</span><br>
                    @endif
                    @if ($enderecoFormatado)
                        <span class="cabecalho-empresa-linha-secundaria">Endereço: {{ $enderecoFormatado }}</span><br>
                    @endif
                    @if ($setorFuncionario?->nome)
                        <span class="cabecalho-empresa-linha-secundaria">Setor: {{ $setorFuncionario->nome }}</span><br>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="titulo-principal">TERMO DE TROCA DE EQUIPAMENTO DE TI</div>

    <div class="secao-texto">
        O(a) {{ $identificacaoPessoa }} <strong>{{ $nomeCompletoMaiusculo }}</strong>
        @if ($funcionario->matricula)
            , matrícula <strong>{{ $funcionario->matricula }}</strong>
        @endif
        @if ($cpfFormatado)
            , CPF <strong>{{ $cpfFormatado }}</strong>
        @endif
        declara que, nesta data, <strong>devolveu</strong> à empresa o equipamento abaixo e <strong>recebeu</strong>
        o equipamento substituto, em perfeitas condições de uso ({{ Str::lower($movimentacao->tipo_troca_rotulo) }}).
    </div>

    <h2 class="subtitulo-secao">Equipamento devolvido</h2>
    <table class="tabela-equipamentos">
        <thead>
            <tr>
                <th>ID Equip.</th>
                <th class="texto-esquerda">Equipamento</th>
                <th>Nº de Série</th>
                <th>Patrimônio</th>
                <th class="texto-esquerda">Condição</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pares as $par)
                @php $equipamento = $par['devolvido']?->equipamento; @endphp
                @if ($equipamento)
                    <tr>
                        <td>{{ $equipamento->id }}</td>
                        <td class="texto-esquerda">@include('relatorios.partials.equipamento-celula')</td>
                        <td>{{ $equipamento->numero_serie }}</td>
                        <td>{{ $equipamento->patrimonio }}</td>
                        <td class="texto-esquerda">
                            {{ $par['devolvido']->motivo_devolucao_rotulo }}
                            @if ($par['devolvido']->observacao)
                                <br><span class="nota-discreta">{{ $par['devolvido']->observacao }}</span>
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <h2 class="subtitulo-secao">Equipamento recebido</h2>
    <table class="tabela-equipamentos">
        <thead>
            <tr>
                <th>ID Equip.</th>
                <th class="texto-esquerda">Equipamento</th>
                <th>Nº de Série</th>
                <th>Patrimônio</th>
                <th>Valor de Compra (R$)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pares as $par)
                @php $equipamento = $par['entregue']; @endphp
                <tr>
                    <td>{{ $equipamento->id }}</td>
                    <td class="texto-esquerda">@include('relatorios.partials.equipamento-celula')</td>
                    <td>{{ $equipamento->numero_serie }}</td>
                    <td>{{ $equipamento->patrimonio }}</td>
                    <td>{{ $valor($equipamento) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($movimentacao->observacao)
        <div class="secao-texto"><strong>Observação:</strong> {{ $movimentacao->observacao }}</div>
    @endif

    <div class="secao-texto" style="margin-top:18px;">
        O equipamento recebido passa a estar sob a responsabilidade do(a) {{ $identificacaoPessoa }}, com os mesmos
        compromissos de guarda, conservação e uso exclusivo para o trabalho previstos no termo de responsabilidade.
        @unless ($funcionario->terceirizado)
            Em caso de perda, extravio, furto, roubo, dano por mau uso ou não devolução, <strong>autorizo expressamente,
                com base no art. 462, §1º da CLT, o desconto dos valores correspondentes em minha folha de pagamento e/ou
                verbas rescisórias</strong>, limitado ao prejuízo efetivamente apurado.
        @endunless
    </div>

    <div style="page-break-inside: avoid">
        <div class="assinatura-container">
            <div class="assinatura-linha">
                <strong>{{ $nomeCompletoMaiusculo }}</strong><br>
                @if ($funcionario->matricula)
                    Matrícula: {{ $funcionario->matricula }}
                @endif
                @if ($cpfFormatado)
                    &nbsp;|&nbsp; CPF: {{ $cpfFormatado }}
                @endif
            </div>
        </div>

        <div class="cidade-data">
            ______________________________, _______ de __________________ de _________ <br>
            <span>(Cidade), (dia) de (mês) de (ano)</span>
        </div>

        <div class="rodape-emissao">
            Termo de troca #{{ $movimentacao->id }} emitido em: {{ $dataHoraEmissao }}<br>
            Impresso em: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
@endsection
