<?php

namespace Database\Seeders;

use App\Models\TipoEquipamento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoEquipamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            'Monitor',
            'Impressora',
            'Teclado',
            'Mouse',
        ];

        foreach ($tipos as $nomeTipo) {
            TipoEquipamento::create([
                'nome' => $nomeTipo,
            ]);
        }
    }
}
