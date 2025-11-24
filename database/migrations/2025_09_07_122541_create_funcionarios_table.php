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
        Schema::create('funcionarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setor_id')->constrained('setores')->restrictOnDelete();

            $table->string('nome', 30);
            $table->string('sobrenome', 50);

            $table->string('cpf', 11);

            $table->string('matricula', 8)->unique()->nullable();
            $table->date('desligado_em')->nullable();
            $table->boolean('ativo')->default(true);
            $table->json('telefones')->nullable();

            $table->boolean('terceirizado')->default(false);

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
        Schema::dropIfExists('funcionarios');
    }
};
