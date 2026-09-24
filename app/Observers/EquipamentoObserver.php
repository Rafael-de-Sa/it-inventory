<?php

namespace App\Observers;

use App\Models\Equipamento;
use App\Models\EquipamentoHistorico;
use Illuminate\Support\Facades\Auth;

/**
 * Registra a linha do tempo do equipamento: o cadastro e toda mudança de status.
 * Empréstimos e devoluções informam o contexto via Equipamento::comHistorico();
 * mudanças sem contexto (ex.: tela de edição) ficam como "alteracao_status".
 */
class EquipamentoObserver
{
    public function created(Equipamento $equipamento): void
    {
        $this->registrar($equipamento, 'cadastro', null, $equipamento->status, $equipamento->consumirContextoHistorico());
    }

    public function updated(Equipamento $equipamento): void
    {
        $contexto = $equipamento->consumirContextoHistorico();

        if (! $equipamento->wasChanged('status')) {
            return;
        }

        $this->registrar(
            $equipamento,
            $contexto['evento'] ?? 'alteracao_status',
            $equipamento->getOriginal('status'),
            $equipamento->status,
            $contexto
        );
    }

    private function registrar(
        Equipamento $equipamento,
        string $evento,
        ?string $statusAnterior,
        ?string $statusNovo,
        array $contexto
    ): void {
        EquipamentoHistorico::create([
            'equipamento_id' => $equipamento->id,
            'evento' => $evento,
            'status_anterior' => $statusAnterior,
            'status_novo' => $statusNovo,
            'movimentacao_id' => $contexto['movimentacao']?->id ?? null,
            'usuario_id' => Auth::id(),
            'observacao' => $contexto['observacao'] ?? null,
            'ocorrido_em' => now(),
        ]);
    }
}
