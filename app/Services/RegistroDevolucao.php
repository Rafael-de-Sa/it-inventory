<?php

namespace App\Services;

use App\Models\Equipamento;
use App\Models\Movimentacao;
use App\Models\MovimentacaoEquipamento;
use Illuminate\Support\Facades\DB;

/**
 * Registra uma devolução (termo de devolução pendente de assinatura): encerra os itens de empréstimo
 * do funcionário, muda o status dos equipamentos conforme o motivo e encerra os termos sem itens em aberto.
 * Usado pela tela de devolução e pela ocorrência que recolhe o equipamento (issue #11).
 */
class RegistroDevolucao
{
    /**
     * @param  array<int, array{motivo?: ?string, observacao?: ?string}>  $itens  equipamento_id => motivo e observação
     */
    public static function registrar(int $setorId, int $funcionarioId, array $itens, ?string $observacaoGeral = null): Movimentacao
    {
        return DB::transaction(function () use ($setorId, $funcionarioId, $itens, $observacaoGeral) {
            $devolucao = Movimentacao::create([
                'setor_id' => $setorId,
                'funcionario_id' => $funcionarioId,
                'observacao' => $observacaoGeral,
                'status' => 'pendente',
                'tipo_movimentacao' => Movimentacao::TIPO_DEVOLUCAO,
            ]);

            $emprestimosEmAberto = MovimentacaoEquipamento::query()
                ->whereIn('equipamento_id', array_keys($itens))
                ->whereNull('devolvido_em')
                ->whereHas('movimentacao', fn ($query) => $query
                    ->where('funcionario_id', $funcionarioId)
                    ->comEmprestimo()
                    ->where('status', '!=', 'cancelada'))
                ->lockForUpdate()
                ->get();

            foreach ($emprestimosEmAberto as $item) {
                $motivo = $itens[$item->equipamento_id]['motivo'] ?? 'devolucao';
                $observacao = $itens[$item->equipamento_id]['observacao'] ?? $observacaoGeral;

                $item->update([
                    'devolucao_movimentacao_id' => $devolucao->id,
                    'devolvido_em' => now()->toDateString(),
                    'motivo_devolucao' => $motivo,
                    'observacao' => $observacao,
                ]);

                $devolucao->equipamentos()->attach($item->equipamento_id, [
                    'motivo_devolucao' => $motivo,
                    'observacao' => $observacao,
                    'devolvido_em' => now()->toDateString(),
                ]);

                $equipamento = Equipamento::query()->lockForUpdate()->find($item->equipamento_id);
                if (! $equipamento) {
                    continue;
                }

                $observacaoHistorico = trim('Motivo: ' . (MovimentacaoEquipamento::MOTIVOS_DEVOLUCAO[$motivo] ?? $motivo) . '. ' . ($observacao ?? ''));

                $equipamento->comHistorico('devolucao', $devolucao, $observacaoHistorico)->update([
                    'status' => MovimentacaoEquipamento::statusEquipamentoAposDevolucao($motivo),
                ]);
            }

            Movimentacao::query()
                ->whereIn('id', $emprestimosEmAberto->pluck('movimentacao_id')->unique())
                ->get()
                ->each(fn (Movimentacao $termo) => $termo->encerrarSeTudoDevolvido());

            return $devolucao;
        });
    }
}
