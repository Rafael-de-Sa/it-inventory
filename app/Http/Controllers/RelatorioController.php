<?php

namespace App\Http\Controllers;

use App\Models\Funcionario;
use App\Models\Movimentacao;
use App\Models\MovimentacaoEquipamento;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RelatorioController extends Controller
{
    public function equipamentosPorFuncionario(Funcionario $funcionario)
    {
        $funcionario->load([
            'setor.empresa',
        ]);

        $listaDeEquipamentosEmUso = MovimentacaoEquipamento::query()
            ->whereNull('devolvido_em')
            ->whereHas('movimentacao', function ($consultaMovimentacao) use ($funcionario) {
                $consultaMovimentacao
                    ->where('funcionario_id', $funcionario->id)
                    ->where('tipo_movimentacao', Movimentacao::TIPO_RESPONSABILIDADE)
                    ->where('status', '!=', 'cancelada');
            })
            ->with([
                'equipamento.tipoEquipamento',
                'movimentacao',
            ])
            ->orderBy('criado_em')
            ->get();

        $dataGeracaoRelatorio = now();

        $pdf = Pdf::loadView('relatorios.funcionarios.equipamentos-por-funcionario', [
            'funcionario'              => $funcionario,
            'listaDeEquipamentosEmUso' => $listaDeEquipamentosEmUso,
            'dataGeracaoRelatorio'     => $dataGeracaoRelatorio,
        ])->setPaper('a4', 'portrait');

        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        $canvas = $dompdf->getCanvas();
        $canvas->page_text(
            520,
            810,
            "Página {PAGE_NUM} de {PAGE_COUNT}",
            null,
            9,
            [0.4, 0.4, 0.4]
        );

        $nomeArquivo = 'relatorio_equipamentos_funcionario_' . $funcionario->id . '.pdf';

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "inline; filename={$nomeArquivo}");
    }
}
