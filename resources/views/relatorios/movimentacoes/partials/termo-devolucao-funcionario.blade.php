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
    if ($rua) {
        $enderecoPartes[] = $rua;
    }
    if ($numero) {
        $enderecoPartes[] = "nº {$numero}";
    }
    if ($bairro) {
        $enderecoPartes[] = $bairro;
    }
    if ($cidade && $estado) {
        $enderecoPartes[] = "{$cidade}/{$estado}";
    } elseif ($cidade) {
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

                @if ($cnpjFormatado)
                    <span class="cabecalho-empresa-linha-secundaria">
                        CNPJ: {{ $cnpjFormatado }}
                    </span><br>
                @endif

                @if ($enderecoFormatado)
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
    TERMO DE DEVOLUÇÃO DE EQUIPAMENTOS DE TI
</div>

<div class="secao-texto">
    O(a) Sr(a). <strong>{{ $nomeCompletoMaiusculo }}</strong>, matrícula
    <strong>{{ $funcionario->matricula }}</strong>
    @if ($cpfFormatado)
        , CPF <strong>{{ $cpfFormatado }}</strong>
    @endif
    declara, para os devidos fins, que devolveu à empresa os equipamentos relacionados abaixo,
    nas condições descritas, ficando ciente de que eventuais danos por mau uso poderão ser objeto
    de análise e responsabilização nos termos da legislação trabalhista aplicável.
</div>

<table class="tabela-equipamentos">
    <thead>
        <tr>
            <th>Item</th>
            <th>ID Equip.</th>
            <th>Descrição</th>
            <th>Nº de Série</th>
            <th>Patrimônio</th>
            <th>Observação Devolução</th>
            <th>Motivo Devolução</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($movimentacao->equipamentos as $indiceEquipamento => $equipamento)
            @php
                $numeroItem = $indiceEquipamento + 1;
                $pivot = $equipamento->pivot;

                $motivosDevolucaoLabels = [
                    'manutencao' => 'Manutenção',
                    'defeito' => 'Defeito',
                    'quebra' => 'Quebra',
                    'devolucao' => 'Devolução',
                ];

                $motivoDevolucaoFormatado = $motivosDevolucaoLabels[$pivot->motivo_devolucao] ?? 'Não informado';
            @endphp
            <tr>
                <td>{{ $numeroItem }}</td>
                <td>{{ $equipamento->id }}</td>
                <td class="texto-esquerda">
                    {{ $equipamento->descricao ?? ($equipamento->nome ?? 'Descrição não informada') }}
                </td>
                <td>{{ $equipamento->numero_serie ?? ($equipamento->serial ?? '') }}</td>
                <td>{{ $equipamento->patrimonio ?? ($equipamento->numero_patrimonio ?? '') }}</td>
                <td class="texto-esquerda">{{ $pivot->observacao }}</td>
                <td class="texto-esquerda">{{ $motivoDevolucaoFormatado }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8">
                    Nenhum equipamento vinculado a esta devolução.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="secao-texto" style="margin-top:18px;">
    Declaro que os equipamentos acima foram devidamente conferidos no ato da devolução e que
    estou ciente de que qualquer divergência poderá ser objeto de apuração interna.
</div>

<div style="page-break-inside: avoid">
    <div class="assinatura-container">
        <div class="assinatura-linha">
            <strong>{{ $nomeCompletoMaiusculo }}</strong><br>
            Matrícula: {{ $funcionario->matricula }}
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
        Termo emitido em:
        {{ $dataHoraEmissao ?? ($dataEmissao ?? '') }}<br>

        Impresso em:
        {{ $dataImpressao ?? now()->format('d/m/Y H:i') }}
    </div>
</div>
