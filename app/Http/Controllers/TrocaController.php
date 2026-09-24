<?php

namespace App\Http\Controllers;

use App\Http\Requests\Movimentacoes\StoreTrocaRequest;
use App\Models\Equipamento;
use App\Models\Movimentacao;
use App\Models\MovimentacaoEquipamento;
use App\Models\Ocorrencia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Termo de troca (issue #10): substitui um equipamento em uso por outro disponível, em uma única movimentação.
 *
 * - O item do empréstimo antigo é devolvido pela troca (devolucao_movimentacao_id) e o equipamento muda de status
 *   conforme o tipo: na troca interna, pela condição informada; na troca pelo fornecedor, "Baixado".
 * - O substituto vira item da troca (um novo empréstimo ao mesmo funcionário), apontando o item que substituiu.
 * - Opcionalmente, a identificação interna (ex.: CELUR01) passa do antigo para o novo.
 */
class TrocaController extends Controller
{
    public function create(Request $request)
    {
        $itensEmUso = MovimentacaoEquipamento::query()
            ->whereNull('devolvido_em')
            ->whereHas('movimentacao', fn ($movimentacoes) => $movimentacoes->comEmprestimo()->where('status', '!=', 'cancelada'))
            ->with(['equipamento.tipoEquipamento', 'movimentacao.funcionario'])
            ->get()
            ->filter(fn (MovimentacaoEquipamento $item) => $item->equipamento)
            ->sortBy(fn (MovimentacaoEquipamento $item) => $item->movimentacao?->funcionario?->nome_completo);

        $equipamentosEmUso = $itensEmUso->mapWithKeys(fn (MovimentacaoEquipamento $item) => [
            $item->equipamento_id => $this->rotuloEquipamento($item->equipamento)
                . ' — ' . ($item->movimentacao?->funcionario?->nome_completo ?? 'Funcionário não encontrado')
                . ($item->movimentacao?->funcionario?->matricula ? " ({$item->movimentacao->funcionario->matricula})" : ''),
        ]);

        $equipamentosDisponiveis = Equipamento::query()
            ->with('tipoEquipamento')
            ->where('ativo', true)
            ->where('status', 'disponivel')
            ->get()
            ->sortBy(fn (Equipamento $equipamento) => [$equipamento->tipoEquipamento?->nome, $equipamento->id])
            ->mapWithKeys(fn (Equipamento $equipamento) => [$equipamento->id => $this->rotuloEquipamento($equipamento)]);

        $ocorrencia = $request->filled('ocorrencia_id') ? Ocorrencia::find($request->integer('ocorrencia_id')) : null;

        return view('movimentacoes.troca.create', [
            'equipamentosEmUso' => $equipamentosEmUso,
            'equipamentosDisponiveis' => $equipamentosDisponiveis,
            'equipamentoAntigoId' => $request->integer('equipamento_id') ?: $ocorrencia?->equipamento_id,
            'ocorrencia' => $ocorrencia,
        ]);
    }

    public function store(StoreTrocaRequest $request)
    {
        $dados = $request->validated();

        $troca = DB::transaction(function () use ($dados) {
            $antigo = Equipamento::query()->lockForUpdate()->findOrFail($dados['equipamento_antigo_id']);
            $novo = Equipamento::query()->lockForUpdate()->findOrFail($dados['equipamento_novo_id']);

            $itemEmprestimo = $antigo->emprestimoEmAberto();
            $funcionario = $itemEmprestimo->movimentacao->funcionario;
            $motivo = $dados['tipo_troca'] === 'fornecedor' ? 'troca_fornecedor' : $dados['motivo'];
            $observacao = $dados['observacao'] ?? null;

            $troca = Movimentacao::create([
                'setor_id' => $funcionario->setor_id ?? $itemEmprestimo->movimentacao->setor_id,
                'funcionario_id' => $funcionario->id,
                'observacao' => $observacao,
                'tipo_movimentacao' => Movimentacao::TIPO_TROCA,
                'tipo_troca' => $dados['tipo_troca'],
            ]);

            $itemEmprestimo->update([
                'devolucao_movimentacao_id' => $troca->id,
                'devolvido_em' => now()->toDateString(),
                'motivo_devolucao' => $motivo,
                'observacao' => $observacao,
            ]);

            $troca->equipamentos()->attach($novo->id, ['substitui_item_id' => $itemEmprestimo->id]);

            $identificacao = $dados['transferir_identificacao'] ? $antigo->identificacao : null;
            $notaIdentificacao = $identificacao ? " Identificação {$identificacao} transferida." : '';

            $antigo->comHistorico('troca', $troca, trim(
                "Substituído pelo #{$novo->id} ({$novo->nome_exibicao}). "
                . Movimentacao::TIPOS_TROCA[$dados['tipo_troca']] . ': '
                . MovimentacaoEquipamento::MOTIVOS_DEVOLUCAO[$motivo] . '.' . $notaIdentificacao
            ))->update([
                'status' => MovimentacaoEquipamento::statusEquipamentoAposDevolucao($motivo),
                ...($identificacao ? ['identificacao' => null] : []),
            ]);

            $novo->comHistorico('troca', $troca, "Substitui o #{$antigo->id} ({$antigo->nome_exibicao}).{$notaIdentificacao}")
                ->update([
                    'status' => 'em_uso',
                    ...($identificacao ? ['identificacao' => $identificacao] : []),
                ]);

            $itemEmprestimo->movimentacao->encerrarSeTudoDevolvido();

            if (! empty($dados['ocorrencia_id'])) {
                $ocorrencia = Ocorrencia::find($dados['ocorrencia_id']);
                $ocorrencia->update([
                    'troca_movimentacao_id' => $troca->id,
                    'solucao' => $ocorrencia->solucao ?: "Troca do equipamento (termo de troca #{$troca->id}).",
                ]);
            }

            return $troca;
        });

        return redirect()
            ->route('movimentacoes.show', $troca)
            ->with('success', "Troca #{$troca->id} registrada. Gere o termo de troca para assinatura.");
    }

    public function termo(Movimentacao $movimentacao)
    {
        abort_unless($movimentacao->tipo_movimentacao === Movimentacao::TIPO_TROCA, 404);

        $comFicha = fn ($consulta) => $consulta->with(['tipoEquipamento', ...Equipamento::RELACOES_FICHA]);

        $movimentacao->load([
            'setor.empresa',
            'funcionario.setor.empresa',
            'equipamentos' => $comFicha,
            'itensDevolvidosAqui.equipamento' => $comFicha,
        ]);

        $pdf = Pdf::loadView('relatorios.movimentacoes.termo-troca', [
            'movimentacao' => $movimentacao,
            'pares' => $this->paresDaTroca($movimentacao),
        ])->setPaper('a4', 'portrait');

        $dompdf = $pdf->getDomPDF();
        $dompdf->render();
        $dompdf->getCanvas()->page_text(520, 810, 'Página {PAGE_NUM} de {PAGE_COUNT}', null, 9, [0.4, 0.4, 0.4]);

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "inline; filename=termo_troca_movimentacao_{$movimentacao->id}.pdf");
    }

    /**
     * Pares "devolvido → entregue" da troca, a partir do item entregue (que aponta o item substituído).
     *
     * @return \Illuminate\Support\Collection<int, array{devolvido: ?MovimentacaoEquipamento, entregue: Equipamento}>
     */
    public static function paresDaTroca(Movimentacao $troca)
    {
        $devolvidos = $troca->itensDevolvidosAqui->keyBy('id');

        return $troca->equipamentos->map(fn (Equipamento $entregue) => [
            'devolvido' => $devolvidos->get($entregue->pivot->substitui_item_id),
            'entregue' => $entregue,
        ]);
    }

    /** "#12 · Celular Samsung Galaxy A05s · CELUR01 · S/N R9XX7067RBR" */
    private function rotuloEquipamento(Equipamento $equipamento): string
    {
        return collect([
            "#{$equipamento->id}",
            trim(($equipamento->tipoEquipamento?->nome ?? '') . ' ' . $equipamento->nome_exibicao),
            $equipamento->identificacao,
            $equipamento->numero_serie ? "S/N {$equipamento->numero_serie}" : null,
        ])->filter()->implode(' · ');
    }
}
