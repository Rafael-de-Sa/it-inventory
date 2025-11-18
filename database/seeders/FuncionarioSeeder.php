<?php

namespace Database\Seeders;

use App\Models\Funcionario;
use App\Models\Setor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FuncionarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setorUm = Setor::first();
        $setorDois = Setor::find(2);

        if (! $setorUm || !$setorDois) {
            $this->command?->warn('Nenhum setor encontrado. Execute primeiro o SetorSeeder.');
            return;
        }

        Funcionario::create([
            'setor_id'     => $setorUm->id,
            'nome'         => 'Rafael',
            'sobrenome'    => 'de Sá',
            'cpf'          => '38421835009',
            'matricula'    => '0001',
            'telefone'     => '44999999999',
            'terceirizado' => false
        ]);


        Funcionario::create([
            'setor_id'     => $setorDois->id,
            'nome'         => 'Carlos',
            'sobrenome'    => 'Oliveira',
            'cpf'          => '84075553043',
            'telefone'     => '44988887777',
            'terceirizado' => true,
        ]);
    }
}
