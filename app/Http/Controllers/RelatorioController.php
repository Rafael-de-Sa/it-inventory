<?php

namespace App\Http\Controllers;

use App\Models\Equipamento;
use App\Models\Funcionario;
use App\Models\Movimentacao;
use App\Models\MovimentacaoEquipamento;
use App\Models\Ocorrencia;
use App\Services\IndicadoresManutencao;
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
                    ->comEmprestimo()
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

        $ocorrencias = $equipamento->ocorrencias()
            ->with(['funcionario', 'troca'])
            ->orderByDesc('reportado_em')
            ->orderByDesc('id')
            ->get();

        $indicadores = IndicadoresManutencao::doEquipamento($equipamento, ocorrencias: $ocorrencias);

        $dataHoraEmissao = now();

        $pdf = Pdf::loadView('relatorios.equipamentos.historico', [
            'equipamento' => $equipamento,
            'ocorrencias' => $ocorrencias,
            'indicadores' => $indicadores,
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

    /** Relatório de uma ocorrência (manutenção), com o total de manutenção acumulado no equipamento. */
    public function ocorrencia(Ocorrencia $ocorrencia)
    {
        $ocorrencia->load(['funcionario', 'usuario.funcionario', 'troca']);

        $equipamento = $ocorrencia->equipamento()->with(['tipoEquipamento', ...Equipamento::RELACOES_FICHA])->firstOrFail();
        $indicadores = IndicadoresManutencao::doEquipamento($equipamento);

        $pdf = Pdf::loadView('relatorios.ocorrencias.ocorrencia', [
            'ocorrencia' => $ocorrencia,
            'equipamento' => $equipamento,
            'indicadores' => $indicadores,
            'dataHoraEmissao' => now(),
        ])->setPaper('a4', 'portrait');

        $dompdf = $pdf->getDomPDF();
        $dompdf->render();
        $dompdf->getCanvas()->page_text(460, 810, 'Página {PAGE_NUM} de {PAGE_COUNT}', null, 9, [0.4, 0.4, 0.4]);

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "inline; filename=relatorio_ocorrencia_{$ocorrencia->id}.pdf");
    }
}
