@extends('relatorios.layouts.base')

@section('titulo_pagina', 'Termo de Responsabilidade de Equipamento')

@section('content')
    @php
        $funcionario = $movimentacao->funcionario;

        $setorFuncionario = $funcionario->setor ?? null;
        $empresaFuncionario = $setorFuncionario->empresa ?? null;

        // Data apenas (se ainda quiser usar em algum lugar)
        $dataEmissao = \Illuminate\Support\Carbon::parse($movimentacao->criado_em ?? now())->format('d/m/Y');

        // Data e hora para o rodapé "Termo emitido em:"
        $dataHoraEmissao = \Illuminate\Support\Carbon::parse($movimentacao->criado_em ?? now())->format('d/m/Y H:i');
    @endphp

    @if (!$funcionario->terceiro)
        @include('relatorios.movimentacoes.partials.termo-responsabilidade-funcionario', [
            'movimentacao' => $movimentacao,
            'funcionario' => $funcionario,
            'setorFuncionario' => $setorFuncionario,
            'empresaFuncionario' => $empresaFuncionario,
            'dataEmissao' => $dataEmissao,
            'dataHoraEmissao' => $dataHoraEmissao,
        ])
    @else
        {{-- Terceiro (vamos montar depois) --}}
        @includeIf('relatorios.movimentacoes.partials.termo-responsabilidade-terceiro', [
            'movimentacao' => $movimentacao,
            'funcionario' => $funcionario,
            'setorFuncionario' => $setorFuncionario,
            'empresaFuncionario' => $empresaFuncionario,
            'dataEmissao' => $dataEmissao,
            'dataHoraEmissao' => $dataHoraEmissao,
        ])
    @endif
@endsection
