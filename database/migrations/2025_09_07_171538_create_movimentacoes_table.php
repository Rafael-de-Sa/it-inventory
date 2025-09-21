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
        Schema::create('movimentacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setor_id')->constrained('setores')->restrictOnDelete();
            $table->foreignId('funcionario_id')->constrained('funcionarios')->restrictOnDelete();

            $table->text('observacao')->nullable();
            $table->string('termo_responsabilidade')->nullable();
            $table->enum('status', ['pendente', 'cancelada', 'finalizada'])->default('pendente');

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
        Schema::dropIfExists('movimentacoes');
    }
};
