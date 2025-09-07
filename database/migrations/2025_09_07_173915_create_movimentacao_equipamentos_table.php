<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movimentacao_equipamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movimentacao_id')->constrained('movimentacoes')->restrictOnDelete();
            $table->foreignId('equipamento_id')->constrained('equipamentos')->restrictOnDelete();

            $table->string('termo_devolucao', 200)->nullable();
            $table->text('observacao')->nullable();
            $table->enum('motivo_devolucao', ['manutencao', 'defeito', 'quebra', 'devolucao'])->default('devolucao');

            $table->date('devolvido_em')->nullable();
            $table->timestamp('criado_em')->nullable();
            $table->timestamp('atualizado_em')->nullable();
            $table->softDeletes('apagado_em');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimentacao_equipamentos');
    }
};
