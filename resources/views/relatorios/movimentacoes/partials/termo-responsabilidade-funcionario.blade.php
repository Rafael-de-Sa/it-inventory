@php
    use App\Support\Mask;

    $razaoSocialEmpresa = $empresaFuncionario->razao_social ?? null;
    $nomeFantasiaEmpresa = $empresaFuncionario->nome_fantasia ?? null;

    $cnpjFormatado = !empty($empresaFuncionario?->cnpj) ? Mask::cnpj($empresaFuncionario->cnpj) : null;

    $cpfFormatado = !empty($funcionario->cpf) ? Mask::cpf($funcionario->cpf) : null;
@endphp

{{-- Cabeçalho do sistema --}}
<div class="cabecalho-sistema">
    {{-- DOMPDF lê melhor usando public_path, certifica que o arquivo está em /public/assets/logo-teste.png --}}
    <img src="{{ public_path('assets/logo-teste.png') }}" alt="Logo IT Inventory" class="cabecalho-sistema-logo">

    <div>
        <div class="cabecalho-sistema-nome">
            IT Inventory
        </div>
        <div style="font-size:10px; color:#4b5563;">
            Sistema de Gestão de Ativos de TI
        </div>
    </div>
</div>

{{-- Cabeçalho da empresa --}}
<div class="cabecalho-empresa">
    @if ($razaoSocialEmpresa)
        <strong>{{ strtoupper($razaoSocialEmpresa) }}</strong><br>
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

    @if (!empty($setorFuncionario?->nome))
        <span class="cabecalho-empresa-linha-secundaria">
            Setor: {{ $setorFuncionario->nome }}
        </span><br>
    @endif
</div>

<div class="titulo-principal">
    TERMO DE RESPONSABILIDADE E AUTORIZAÇÃO PARA DESCONTO EM FOLHA DE PAGAMENTO
</div>

<div class="secao-texto">
    O abaixo-assinado, Sr(a)
    <strong>{{ mb_strtoupper($funcionario->nome . ' ' . $funcionario->sobrenome, 'UTF-8') }}</strong>,
    portador(a) da matrícula
    <strong>{{ $funcionario->matricula }}</strong>,
    @if ($cpfFormatado)
        CPF <strong>{{ $cpfFormatado }}</strong>,
    @endif
    firmo o presente
    <strong>TERMO DE RESPONSABILIDADE E AUTORIZAÇÃO</strong>
    para desconto de danos que porventura venha causar à empresa, nos termos do
    <strong>Art. 462, § 1.º da Consolidação das Leis do Trabalho (CLT)</strong>.
</div>

<div class="secao-texto">
    Obriga-se o(a) funcionário(a) supracitado(a) a zelar pelos equipamentos que lhe foram confiados,
    utilizando-os exclusivamente para atividades de trabalho, mantendo-os em perfeitas condições
    de uso, conservação e segurança, conforme descritos na relação abaixo.
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
    <strong>Compromissos:</strong><br>
    Ao receber os itens acima, o(a) funcionário(a) assume o compromisso de:
    <ul style="margin-top:6px; margin-left:20px;">
        <li>Realizar o controle e a guarda adequada dos equipamentos;</li>
        <li>Garantir o uso exclusivo para atividades relacionadas ao trabalho;</li>
        <li>Comunicar imediatamente qualquer dano, perda ou necessidade de manutenção;</li>
        <li>Zelar pela conservação dos dispositivos e seus acessórios (capinha, película, chip de dados, etc.);</li>
        <li>Não permitir o uso dos equipamentos por terceiros não autorizados;</li>
        <li>Devolver os itens em perfeitas condições de funcionamento quando solicitado pela empresa;</li>
    </ul>
</div>

<div class="secao-texto">
    Em caso de perda, extravio, furto, roubo, dano decorrente de mau uso ou não devolução dos equipamentos
    sob minha responsabilidade, <strong>autorizo expressamente o desconto em folha de pagamento e/ou
        verbas rescisórias</strong>, até o limite permitido pela legislação vigente, para ressarcimento
    dos prejuízos causados à empresa.
</div>

{{-- Linha de data em branco para preenchimento manual --}}
<div class="rodape-local-data">
    <div>
        ____________________, ____ de _______________ de _____
    </div>
    <div style="font-size:10px; color:#4b5563;">
        (Cidade), (dia) de (mês) de (ano)
    </div>
</div>

{{-- Área de assinaturas com nome completo, matrícula e CPF mascarado --}}
<div class="area-assinaturas">
    <table>
        <tr>
            <td class="linha-assinatura">
                <strong>{{ mb_strtoupper($funcionario->nome . ' ' . $funcionario->sobrenome, 'UTF-8') }}</strong><br>
                Matrícula: {{ $funcionario->matricula }}
                @if ($cpfFormatado)
                    &nbsp;|&nbsp; CPF: {{ $cpfFormatado }}
                @endif
            </td>
            <td></td>
        </tr>
    </table>
</div>

{{-- Rodapé com data/hora real de emissão --}}
<div class="rodape-local-data" style="text-align:right; font-size:10px; margin-top:12px;">
    Termo emitido em:
    {{ $dataHoraEmissao ?? ($dataEmissao ?? '') }}
</div>
