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
        Schema::create('equipamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_equipamento_id')->constrained('tipo_equipamentos')->restrictOnDelete();
            $table->date('data_compra')->nullable();
            $table->decimal('valor_compra', 12, 2)->nullable();
            $table->enum('status', ['em_uso', 'defeituoso', 'descartado', 'disponivel', 'em_manutencao'])->default('disponivel');
            $table->boolean('ativo')->default(true);
            $table->text('descricao');
            $table->string('patrimonio')->unique()->nullable();
            $table->string('numero_serie')->unique()->nullable();

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
        Schema::dropIfExists('equipamentos');
    }
};
