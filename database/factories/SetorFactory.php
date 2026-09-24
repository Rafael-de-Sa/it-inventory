<?php

namespace Database\Factories;

use App\Models\Empresa;
use App\Models\Setor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Setor>
 */
class SetorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'empresa_id' => Empresa::factory(),
            'nome' => fake()->randomElement(['Tecnologia da Informação', 'Departamento Pessoal', 'Financeiro', 'Comercial']),
            'ativo' => true,
        ];
    }
}
