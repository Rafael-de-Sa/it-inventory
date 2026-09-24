<?php

namespace Database\Seeders;

use App\Enums\CategoriaEquipamento;
use App\Models\TipoEquipamento;
use Illuminate\Database\Seeder;

class TipoEquipamentoSeeder extends Seeder
{
    /**
     * Tipos iniciais com a categoria da ficha técnica. Pode ser executado de novo sem duplicar.
     */
    public function run(): void
    {
        $tipos = [
            'Desktop' => CategoriaEquipamento::COMPUTADOR,
            'Notebook' => CategoriaEquipamento::COMPUTADOR,
            'Servidor' => CategoriaEquipamento::COMPUTADOR,
            'Monitor' => CategoriaEquipamento::MONITOR,
            'Impressora' => CategoriaEquipamento::IMPRESSORA,
            'Celular' => CategoriaEquipamento::DISPOSITIVO_MOVEL,
            'Smart POS' => CategoriaEquipamento::DISPOSITIVO_MOVEL,
            'PINPad' => CategoriaEquipamento::GENERICO,
            'Teclado' => CategoriaEquipamento::GENERICO,
            'Mouse' => CategoriaEquipamento::GENERICO,
        ];

        foreach ($tipos as $nome => $categoria) {
            TipoEquipamento::firstOrCreate(['nome' => $nome], ['categoria' => $categoria]);
        }
    }
}
