<?php

namespace Database\Seeders;

use App\Models\Empresa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Empresa::create([
            'nome_fantasia' => 'Empresa Demonstração',
            'razao_social' => 'Empresa Demonstracao LTDA',
            'cnpj' => '20449288000193',
            'rua' => 'Rua do Centro',
            'numero' => '1234',
            'complemento' => 'Sala 01',
            'bairro' => 'Centro',
            'cidade' => 'Umuarama',
            'estado' => 'PR',
            'cep' => '87501000',
            'email' => 'contato@demonstracao.com',
            'telefone' => '44999999999',
        ]);
        Empresa::create([
            'nome_fantasia' => 'Empresa Demonstração 2',
            'razao_social' => 'Empresa Demonstracao Dois LTDA',
            'cnpj' => '76828686000175',
            'rua' => 'Avenida do Centro',
            'numero' => '1234',
            'bairro' => 'Zona II',
            'cidade' => 'Umuarama',
            'estado' => 'PR',
            'cep' => '87502000',
            'email' => 'contato@demonstracaodois.com',
            'telefone' => '44999999999',
        ]);
    }
}
