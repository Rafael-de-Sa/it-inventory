<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Termo de devolução gerado quando a ocorrência recolhe o equipamento do funcionário (issue #11).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ocorrencias', function (Blueprint $table) {
            $table->foreignId('devolucao_movimentacao_id')->nullable()->after('troca_movimentacao_id')
                ->constrained('movimentacoes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ocorrencias', function (Blueprint $table) {
            $table->dropConstrainedForeignId('devolucao_movimentacao_id');
        });
    }
};
