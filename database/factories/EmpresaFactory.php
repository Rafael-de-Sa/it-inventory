<?php

namespace Database\Factories;

use App\Enums\Uf;
use App\Models\Empresa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Empresa>
 */
class EmpresaFactory extends Factory
{
    public function definition(): array
    {
        $faker = fake('pt_BR');

        return [
            'nome_fantasia' => mb_substr($faker->company(), 0, 100),
            'razao_social' => mb_substr($faker->company() . ' LTDA', 0, 100),
            'cnpj' => $faker->unique()->cnpj(false),
            'logradouro' => mb_substr($faker->streetName(), 0, 100),
            'numero' => (string) $faker->numberBetween(1, 9999),
            'complemento' => null,
            'bairro' => 'Centro',
            'cidade' => mb_substr($faker->city(), 0, 30),
            'estado' => $faker->randomElement(Uf::cases())->value,
            'cep' => $faker->numerify('########'),
            'email' => $faker->unique()->safeEmail(),
            'telefone' => '44999999999',
            'ativo' => true,
        ];
    }

    public function inativa(): static
    {
        return $this->state(['ativo' => false]);
    }
}
