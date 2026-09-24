<?php

namespace Database\Factories;

use App\Models\Funcionario;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Usuario>
 */
class UsuarioFactory extends Factory
{
    public function definition(): array
    {
        return [
            'funcionario_id' => Funcionario::factory(),
            'email' => fake()->unique()->safeEmail(),
            'senha' => 'senha-de-teste',
            'ativo' => true,
        ];
    }
}
