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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funcionario_id')->unique()->constrained('funcionarios')->restrictOnDelete();

            //validar a questão do e-mail (se um colaborador for deligado e posteriormente recontratado pode haver o mesmo e-mail mas com status inativo no cadastro anterior)
            $table->string('email', 100);
            $table->string('senha');
            $table->boolean('ativo')->default(true);
            $table->dateTime('ultimo_login')->nullable();

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
        Schema::dropIfExists('usuarios');
    }
};
