<?php

namespace Database\Factories;

use App\Models\Equipamento;
use App\Models\Movimentacao;
use App\Models\MovimentacaoEquipamento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Item de um termo de responsabilidade, ainda não devolvido.
 *
 * @extends Factory<MovimentacaoEquipamento>
 */
class MovimentacaoEquipamentoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'movimentacao_id' => Movimentacao::factory(),
            'equipamento_id' => Equipamento::factory()->emUso(),
            'motivo_devolucao' => 'devolucao',
            'devolvido_em' => null,
        ];
    }

    public function devolvido(?Movimentacao $devolucao = null): static
    {
        return $this->state([
            'devolvido_em' => today(),
            'devolucao_movimentacao_id' => $devolucao?->id,
        ]);
    }
}
