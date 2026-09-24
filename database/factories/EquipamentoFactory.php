<?php

namespace Database\Factories;

use App\Models\Equipamento;
use App\Models\TipoEquipamento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Equipamento>
 */
class EquipamentoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tipo_equipamento_id' => TipoEquipamento::factory(),
            'descricao' => fake('pt_BR')->sentence(3),
            'patrimonio' => (string) fake()->unique()->numberBetween(100000, 999999),
            'numero_serie' => fake()->unique()->bothify('SN-########'),
            'status' => 'disponivel',
            'ativo' => true,
        ];
    }

    public function emUso(): static
    {
        return $this->state(['status' => 'em_uso']);
    }
}
