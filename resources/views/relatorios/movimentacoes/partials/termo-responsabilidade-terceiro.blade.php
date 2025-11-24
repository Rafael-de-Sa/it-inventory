@php
    use App\Support\Mask;
    use Illuminate\Support\Str;

    $razaoSocialEmpresa = $empresaFuncionario->razao_social ?? null;
    $nomeFantasiaEmpresa = $empresaFuncionario->nome_fantasia ?? null;

    $cnpjFormatado = !empty($empresaFuncionario?->cnpj) ? Mask::cnpj($empresaFuncionario->cnpj) : null;

    $cpfFormatado = !empty($funcionario->cpf) ? Mask::cpf($funcionario->cpf) : null;

    $rua = $empresaFuncionario->rua ?? null;
    $numero = $empresaFuncionario->numero ?? null;
    $bairro = $empresaFuncionario->bairro ?? null;
    $cidade = $empresaFuncionario->cidade ?? null;
    $estado = $empresaFuncionario->estado ?? null;

    $enderecoPartes = [];

    if (!empty($rua)) {
        $enderecoPartes[] = $rua;
    }

    if (!empty($numero)) {
        $enderecoPartes[] = "nº {$numero}";
    }

    if (!empty($bairro)) {
        $enderecoPartes[] = $bairro;
    }

    if (!empty($cidade) && !empty($estado)) {
        $enderecoPartes[] = "{$cidade}/{$estado}";
    } elseif (!empty($cidade)) {
        $enderecoPartes[] = $cidade;
    }

    $enderecoFormatado = implode(' – ', $enderecoPartes);

    $nomeCompleto = trim(($funcionario->nome ?? '') . ' ' . ($funcionario->sobrenome ?? ''));
    $nomeCompletoMaiusculo = Str::upper($nomeCompleto);
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
    TERMO DE RESPONSABILIDADE DE EQUIPAMENTOS (TERCEIRIZADO)
</div>

<div class="secao-texto">
    O(a) abaixo-assinado(a),
    <strong>{{ $nomeCompletoMaiusculo }}</strong>,
    @if ($cpfFormatado)
        portador(a) do CPF <strong>{{ $cpfFormatado }}</strong>,
    @endif
    declara, para os devidos fins, que recebeu da empresa
    <strong>{{ Str::upper($razaoSocialEmpresa ?? 'NÃO INFORMADA') }}</strong>
    os equipamentos descritos na relação abaixo, comprometendo-se a zelar pela
    boa utilização, guarda e conservação dos mesmos enquanto estiverem sob sua
    responsabilidade.
</div>

<div class="secao-texto">
    Declara ainda estar ciente de que os equipamentos ora entregues são
    de propriedade exclusiva da empresa, devendo ser utilizados
    <strong>exclusivamente para fins profissionais</strong> e nas atividades para as quais
    foi designado(a), sendo vedado seu uso particular, cessão ou empréstimo a
    terceiros.
</div>

<table class="tabela-equipamentos">
    <thead>
        <tr>
            <th>Item</th>
            <th>ID Equip.</th>
            <th>Descrição</th>
            <th>Nº de Série</th>
            <th>Patrimônio</th>
            <th>Valor de Compra (R$)</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($movimentacao->equipamentos as $indiceEquipamento => $equipamento)
            @php
                $numeroItem = $indiceEquipamento + 1;
                $valorCompraEquipamento = $equipamento->valor_compra ?? null;
            @endphp
            <tr>
                <td>{{ $numeroItem }}</td>
                <td>{{ $equipamento->id }}</td>
                <td class="texto-esquerda">
                    {{ $equipamento->descricao ?? ($equipamento->nome ?? 'Descrição não informada') }}
                </td>
                <td>{{ $equipamento->numero_serie ?? ($equipamento->serial ?? '') }}</td>
                <td>{{ $equipamento->patrimonio ?? ($equipamento->numero_patrimonio ?? '') }}</td>
                <td>
                    @if (!is_null($valorCompraEquipamento))
                        {{ number_format($valorCompraEquipamento, 2, ',', '.') }}
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6">
                    Nenhum equipamento vinculado a esta movimentação.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="secao-texto" style="margin-top:18px;">
    <strong>Compromissos do Responsável:</strong><br>
    Ao receber os itens acima, o(a) terceirizado(a) compromete-se a:
    <ul style="margin-top:6px; margin-left:20px;">
        <li>Manter os equipamentos sob sua guarda e em perfeito estado de conservação;</li>
        <li>Utilizá-los exclusivamente para as atividades profissionais que lhe forem atribuídas;</li>
        <li>Comunicar imediatamente à contratante qualquer dano, perda, furto ou irregularidade observada;</li>
        <li>Responsabilizar-se por danos decorrentes de mau uso, negligência ou descuido;</li>
        <li>Devolver todos os equipamentos e acessórios em perfeitas condições ao término da prestação de serviços;</li>
    </ul>
</div>

<div class="secao-texto">
    O(a) terceirizado(a) declara estar ciente de que a não devolução dos equipamentos,
    bem como eventuais danos decorrentes de uso indevido, implicarão em
    <strong>responsabilidade civil direta</strong>, sujeita à reparação integral dos prejuízos
    materiais causados à empresa.
</div>

<div style="page-break-inside: avoid">
    <div class="assinatura-container">
        <div class="assinatura-linha">
            <strong>{{ $nomeCompletoMaiusculo }}</strong><br>
            @if ($cpfFormatado)
                CPF: {{ $cpfFormatado }}
            @endif
        </div>
    </div>

    <div class="cidade-data">
        ______________________________, _______ de __________________ de _________ <br>
        <span>(Cidade), (dia) de (mês) de (ano)</span>
    </div>

    <div class="rodape-emissao">
        Termo emitido em:
        {{ $dataHoraEmissao ?? ($dataEmissao ?? '') }}<br>

        Impresso em:
        {{ $dataImpressao ?? now()->format('d/m/Y H:i') }}
    </div>
</div>
