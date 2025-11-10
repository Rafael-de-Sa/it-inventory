@extends('relatorios.layouts.base')

@section('titulo_pagina', 'Termo de Responsabilidade de Equipamento')

@section('content')
    @php
        $funcionario = $movimentacao->funcionario;

        $setorFuncionario = $funcionario->setor ?? null;
        $empresaFuncionario = $setorFuncionario->empresa ?? null;

        $dataEmissao = \Illuminate\Support\Carbon::parse($movimentacao->criado_em ?? now())->format('d/m/Y');

        $dataHoraEmissao = \Illuminate\Support\Carbon::parse($movimentacao->criado_em ?? now())->format('d/m/Y H:i');
    @endphp

    @if (!$funcionario->terceirizado)
        @include('relatorios.movimentacoes.partials.termo-responsabilidade-funcionario', [
            'movimentacao' => $movimentacao,
            'funcionario' => $funcionario,
            'setorFuncionario' => $setorFuncionario,
            'empresaFuncionario' => $empresaFuncionario,
            'dataEmissao' => $dataEmissao,
            'dataHoraEmissao' => $dataHoraEmissao,
        ])
    @else
        @include('relatorios.movimentacoes.partials.termo-responsabilidade-terceiro', [
            'movimentacao' => $movimentacao,
            'funcionario' => $funcionario,
            'setorFuncionario' => $setorFuncionario,
            'empresaFuncionario' => $empresaFuncionario,
            'dataEmissao' => $dataEmissao,
            'dataHoraEmissao' => $dataHoraEmissao,
        ])
    @endif
@endsection
