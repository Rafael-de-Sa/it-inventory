<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Categoria do tipo (App\Enums\CategoriaEquipamento): define a tabela da ficha técnica.
     * Os tipos existentes são classificados pelo nome; os demais ficam como "generico".
     */
    public function up(): void
    {
        Schema::table('tipo_equipamentos', function (Blueprint $table) {
            $table->string('categoria', 20)->default('generico')->after('nome');
        });

        $regras = [
            'computador' => ['%notebook%', '%desktop%', '%computador%', '%servidor%', '%all in one%', '%all-in-one%'],
            'monitor' => ['%monitor%'],
            'impressora' => ['%impressora%'],
            'dispositivo_movel' => ['%celular%', '%smartphone%', '%smart pos%', '%maquininha%', '%moderninha%'],
        ];

        foreach ($regras as $categoria => $padroes) {
            DB::table('tipo_equipamentos')
                ->where(function ($query) use ($padroes) {
                    foreach ($padroes as $padrao) {
                        $query->orWhereRaw('LOWER(nome) LIKE ?', [$padrao]);
                    }
                })
                ->update(['categoria' => $categoria]);
        }
    }

    public function down(): void
    {
        Schema::table('tipo_equipamentos', function (Blueprint $table) {
            $table->dropColumn('categoria');
        });
    }
};
