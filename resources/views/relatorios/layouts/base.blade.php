<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <title>@yield('titulo_pagina', 'Documento')</title>

    <style>
        /*
         * Paleta dos PDFs (mesma do sistema; o dompdf não suporta variáveis CSS):
         *   texto #18181b · texto secundário #52525b · bordas #e4e4e7 · fundo suave #f4f4f5
         *   destaque (brand) #15803d · destaque escuro #166534
         * Ao trocar a cor de destaque em resources/css/app.css, atualize os tons de destaque aqui.
         */

        /* ================== BASE ================== */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11.5px;
            line-height: 1.45;
            margin: 0;
            padding: 0;
            color: #18181b;
        }

        /* ================== CABEÇALHO SISTEMA ================== */
        /* Cabeçalho geral (sistema à esquerda, empresa à direita) */
        .cabecalho-geral {
            width: 100%;
            margin-bottom: 6px;
        }

        .cabecalho-geral td {
            vertical-align: middle;
        }

        .cabecalho-col-sistema {
            width: 30%;
            text-align: center;
        }

        .cabecalho-col-empresa {
            width: 70%;
            text-align: right;
        }

        .cabecalho-sistema {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
        }

        .cabecalho-sistema-logo {
            width: 60px;
            height: 60px;
            border-radius: 9999px;
            border: 1px solid #e4e4e7;
            object-fit: cover;
        }

        .cabecalho-sistema-nome {
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        /* ================== CABEÇALHO EMPRESA ================== */
        .cabecalho-empresa {
            text-align: right;
            margin-bottom: 10px;
            font-size: 11px;
        }

        .cabecalho-empresa strong {
            font-size: 12px;
        }

        .cabecalho-empresa-linha-secundaria {
            color: #52525b;
        }

        /* ================== TÍTULO ================== */
        .titulo-principal {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin: 14px 0 18px 0;
            font-size: 14px;
            color: #18181b;
            border-top: 1px solid #e4e4e7;
            border-bottom: 2px solid #15803d;
            padding: 8px 0;
        }

        .subtitulo-secao {
            font-size: 12px;
            font-weight: bold;
            color: #166534;
            margin: 16px 0 6px 0;
            padding-bottom: 4px;
            border-bottom: 1px solid #e4e4e7;
        }

        /* ================== TEXTOS ================== */
        .secao-texto {
            margin-bottom: 10px;
            text-align: justify;
        }

        .secao-texto ul {
            margin-top: 6px;
            margin-left: 18px;
            padding-left: 0;
        }

        .secao-texto ul li {
            margin-bottom: 2px;
        }

        /* ================== TABELA DE EQUIPAMENTOS ================== */
        .tabela-equipamentos {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            font-size: 11px;
        }

        .tabela-equipamentos thead {
            display: table-header-group;
        }

        .tabela-equipamentos th,
        .tabela-equipamentos td {
            border-bottom: 1px solid #e4e4e7;
            padding: 5px 6px;
        }

        .tabela-equipamentos th {
            text-align: center;
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            -webkit-print-color-adjust: exact !important;
            background-color: #f4f4f5 !important;
            color: #3f3f46 !important;
            border-top: 1px solid #d4d4d8;
            border-bottom: 1px solid #d4d4d8;
        }

        .tabela-equipamentos td {
            vertical-align: top;
            text-align: center;
        }

        .tabela-equipamentos th.texto-esquerda,
        .tabela-equipamentos td.texto-esquerda {
            text-align: left;
        }

        .tabela-equipamentos tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        /* ================== RODAPÉ / ASSINATURA ================== */
        .rodape-local-data {
            margin-top: 24px;
            font-size: 11px;
        }

        .area-assinaturas {
            margin-top: 30px;
            width: 100%;
        }

        .area-assinaturas table {
            width: 100%;
        }

        .linha-assinatura {
            width: 45%;
            border-top: 1px solid #18181b;
            text-align: center;
            font-size: 11px;
            padding-top: 4px;
        }

        @page {
            size: A4 portrait;
            margin: 20mm 15mm 20mm 15mm;
        }

        @bottom-right {
            content: "Página " counter(page) " de " counter(pages);
            font-size: 10px;
            color: #52525b;
        }

        body {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        table {
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        thead {
            display: table-header-group;
        }

        /* ================== ASSINATURAS / RODAPÉ ================== */
        .espaco-antes-assinatura {
            height: 60px;
        }

        .assinatura-container {
            text-align: center;
            margin-top: 60px;
        }

        .assinatura-linha {
            border-top: 1px solid #18181b;
            width: 60%;
            margin: 0 auto;
            padding-top: 4px;
        }

        .cidade-data {
            margin-top: 30px;
            font-size: 11px;
            text-align: right;
        }

        .cidade-data span {
            color: #52525b;
        }

        .rodape-emissao {
            margin-top: 14px;
            padding-top: 6px;
            border-top: 1px solid #e4e4e7;
            font-size: 9.5px;
            color: #52525b;
            text-align: right;
        }
    </style>
</head>

<body>
    @yield('content')
</body>

</html>
