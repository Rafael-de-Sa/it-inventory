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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nome_fantasia', 100);
            $table->string('razao_social', 100);
            $table->string('cnpj', 14)->unique();
            $table->string('rua', 100);
            $table->string('numero', 8);
            $table->string('complemento', 50)->nullable();
            $table->string('bairro', 50)->nullable();
            $table->string('cidade', 30);
            $table->string('estado', 2);
            $table->string('cep', 8);
            $table->string('site', 40)->nullable();
            $table->string('email', 60);
            $table->boolean('ativo')->default(true);
            $table->json('telefones')->nullable();
            $table->string('caminho_logo')->nullable();

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
        Schema::dropIfExists('empresas');
    }
};
