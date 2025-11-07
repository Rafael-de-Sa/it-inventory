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
            width: 40px;
            height: 40px;
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
            background-color: #065f46;
            /* verde escuro */
            color: #f9fafb;
            /* quase branco */
        }

        .tabela-equipamentos th,
        .tabela-equipamentos td {
            border: 1px solid #9ca3af;
            /* cinza borda */
            padding: 4px 6px;
        }

        .tabela-equipamentos th {
            text-align: center;
            font-weight: bold;
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
    </style>
</head>

<body>
    @yield('content')
</body>

</html>
