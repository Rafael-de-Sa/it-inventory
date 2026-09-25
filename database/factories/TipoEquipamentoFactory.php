<?php

namespace Database\Factories;

use App\Enums\CategoriaEquipamento;
use App\Models\TipoEquipamento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TipoEquipamento>
 */
class TipoEquipamentoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => fake()->unique()->bothify('Tipo ####'),
            'categoria' => CategoriaEquipamento::GENERICO,
            'ativo' => true,
        ];
    }

    public function categoria(CategoriaEquipamento $categoria): static
    {
        return $this->state(['categoria' => $categoria]);
    }
}
