<?php

namespace App\Http\Controllers;

use App\Models\Equipamento;
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
                'equipamento' => fn ($consulta) => $consulta->with(['tipoEquipamento', ...Equipamento::RELACOES_FICHA]),
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

    public function historicoEquipamento(Equipamento $equipamento)
    {
        $equipamento->load(['tipoEquipamento', ...Equipamento::RELACOES_FICHA]);

        $listaMovimentacoesResponsabilidade = MovimentacaoEquipamento::query()
            ->historicoResponsabilidadePorEquipamento($equipamento->id)
            ->get();

        $linhaDoTempo = $equipamento->historicos()
            ->with([
                'movimentacao' => fn ($query) => $query->withTrashed(),
                'usuario' => fn ($query) => $query->withTrashed()->with([
                    'funcionario' => fn ($queryFuncionario) => $queryFuncionario->withTrashed(),
                ]),
            ])
            ->orderByDesc('ocorrido_em')
            ->orderByDesc('id')
            ->get();

        $dataHoraEmissao = now();

        $pdf = Pdf::loadView('relatorios.equipamentos.historico', [
            'equipamento' => $equipamento,
            'listaMovimentacoesResponsabilidade' => $listaMovimentacoesResponsabilidade,
            'linhaDoTempo' => $linhaDoTempo,
            'dataHoraEmissao' => $dataHoraEmissao,
        ])->setPaper('a4', 'portrait');

        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        $canvas = $dompdf->getCanvas();

        $canvas->page_text(
            460,
            810,
            'Página {PAGE_NUM} de {PAGE_COUNT}',
            null,
            9,
            [0.4, 0.4, 0.4]
        );

        $nomeArquivo = 'relatorio_historico_equipamento_' . $equipamento->id . '.pdf';

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "inline; filename={$nomeArquivo}");
    }
}
