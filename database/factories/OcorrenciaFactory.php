<?php

namespace Database\Factories;

use App\Models\Equipamento;
use App\Models\Ocorrencia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Ocorrência aberta (sem liberação), sem efeito no status do equipamento.
 *
 * @extends Factory<Ocorrencia>
 */
class OcorrenciaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'equipamento_id' => Equipamento::factory(),
            'reportado_em' => today(),
            'problema' => 'Erro SecStatus 0x2014',
            'canal' => 'GLPI',
            'protocolo' => (string) fake()->numberBetween(10000, 99999),
        ];
    }

    public function resolvida(): static
    {
        return $this->state(['liberado_em' => today(), 'solucao' => 'Limpeza do app']);
    }
}
