<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Custo da manutenção para a empresa e quem a fez (issue #11), separados do valor cobrado do colaborador.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ocorrencias', function (Blueprint $table) {
            $table->decimal('custo_manutencao', 10, 2)->nullable()->after('valor_cobrado');
            $table->string('fornecedor', 80)->nullable()->after('custo_manutencao');
        });
    }

    public function down(): void
    {
        Schema::table('ocorrencias', function (Blueprint $table) {
            $table->dropColumn(['custo_manutencao', 'fornecedor']);
        });
    }
};
