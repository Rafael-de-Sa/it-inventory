<?php

namespace Database\Factories;

use App\Models\Funcionario;
use App\Models\Setor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Funcionário próprio, ativo, admitido há dois anos.
 *
 * @extends Factory<Funcionario>
 */
class FuncionarioFactory extends Factory
{
    public function definition(): array
    {
        $faker = fake('pt_BR');

        return [
            'setor_id' => Setor::factory(),
            'nome' => mb_substr($faker->firstName(), 0, 30),
            'sobrenome' => mb_substr($faker->lastName(), 0, 50),
            'cpf' => $faker->unique()->cpf(false),
            'matricula' => (string) $faker->unique()->numberBetween(1000, 99999999),
            'admitido_em' => today()->subYears(2),
            'desligado_em' => null,
            'telefone' => '44988887777',
            'terceirizado' => false,
            'ativo' => true,
        ];
    }

    public function terceirizado(): static
    {
        return $this->state(['terceirizado' => true, 'matricula' => null]);
    }

    public function desligado(?string $data = null): static
    {
        return $this->state([
            'desligado_em' => $data ?? today()->subDay()->toDateString(),
            'ativo' => false,
        ]);
    }
}
