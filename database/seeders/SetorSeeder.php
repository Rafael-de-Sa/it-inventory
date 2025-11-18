<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Models\Setor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SetorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $empresaPrincipal = Empresa::first();

        if (! $empresaPrincipal) {
            $this->command?->warn('Nenhuma empresa encontrada. Execute primeiro o EmpresaSeeder.');
            return;
        }


        Setor::create([
            'empresa_id' => $empresaPrincipal->id,
            'nome' => 'Tecnologia da Informação',
        ]);

        Setor::create([
            'empresa_id' => $empresaPrincipal->id,
            'nome' => 'Departamento Pessoal'
        ]);
    }
}
