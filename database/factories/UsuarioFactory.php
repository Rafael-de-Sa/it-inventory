<?php

namespace Database\Factories;

use App\Enums\Perfil;
use App\Models\Funcionario;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Usuário do perfil TIC (acesso completo) por padrão.
 *
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
            'perfil' => Perfil::TIC,
            'ativo' => true,
        ];
    }

    public function departamentoPessoal(): static
    {
        return $this->state(['perfil' => Perfil::DP]);
    }
}
