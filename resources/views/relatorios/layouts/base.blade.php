<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <title>@yield('titulo_pagina', 'Documento')</title>

    <style>
        /* ================== BASE ================== */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 25px 35px;
            color: #111827;
            /* cinza bem escuro */
        }

        /* ================== CABEÇALHO SISTEMA ================== */
        .cabecalho-sistema {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
        }

        .cabecalho-sistema-logo {
            width: 70px;
            height: 70px;
            border-radius: 9999px;
            border: 1px solid #16a34a;
            /* verde */
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
            text-align: left;
            margin-bottom: 10px;
            font-size: 11px;
        }

        .cabecalho-empresa strong {
            font-size: 12px;
        }

        .cabecalho-empresa-linha-secundaria {
            color: #4b5563;
            /* cinza méd. */
        }

        /* ================== TÍTULO ================== */
        .titulo-principal {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            margin: 12px 0 18px 0;
            font-size: 14px;
            color: #14532d;
            /* verde mais escuro */
            border-bottom: 1px solid #16a34a;
            padding-bottom: 4px;
            border-top: 1px solid #16a34a;
            padding-top: 4px;
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
            background-color: #065f46 !important;
            color: #f9fafb !important;
        }

        .tabela-equipamentos th,
        .tabela-equipamentos td {
            border: 1px solid #9ca3af;
            padding: 4px 6px;
        }

        .tabela-equipamentos th {
            text-align: center;
            font-weight: bold;
            -webkit-print-color-adjust: exact !important;
            background-color: #065f46 !important;
            color: #f9fafb !important;
        }

        .tabela-equipamentos td {
            vertical-align: top;
            text-align: center;
            /* centraliza por padrão */
        }

        .tabela-equipamentos td.texto-esquerda {
            text-align: left;
            /* para a descrição ficar mais legível */
        }

        .tabela-equipamentos tbody tr:nth-child(even) {
            background-color: #f3f4f6;
            /* listras suaves */
        }

        /* ================== RODAPÉ / ASSINATURA ================== */
        .rodape-local-data {
            margin-top: 24px;
            font-size: 11px;
        }

        .area-assinaturas {
            margin-top: 40px;
            width: 100%;
        }

        .area-assinaturas table {
            width: 100%;
        }

        .linha-assinatura {
            width: 45%;
            border-top: 1px solid #111827;
            text-align: center;
            font-size: 11px;
            padding-top: 4px;
        }

        @page {
            margin: 25px 35px;
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
            margin-top: 40px;
        }

        .assinatura-linha {
            border-top: 1px solid #111827;
            width: 60%;
            margin: 0 auto;
            padding-top: 4px;
        }

        .cidade-data {
            margin-top: 60px;
            font-size: 11px;
            text-align: right;
        }

        .cidade-data span {
            color: #4b5563;
        }

        .rodape-emissao {
            margin-top: 20px;
            font-size: 10px;
            color: #4b5563;
            text-align: right;
        }
    </style>
</head>

<body>
    @yield('content')
</body>

</html>
