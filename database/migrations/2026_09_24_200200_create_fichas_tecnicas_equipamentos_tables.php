<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ficha técnica por categoria de tipo (App\Enums\CategoriaEquipamento), 1:1 com equipamentos.
     * MACs e IMEIs são únicos; o MySQL permite vários NULL em colunas únicas.
     * Senhas, chaves de ativação e chips não são armazenados.
     */
    public function up(): void
    {
        Schema::create('computadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipamento_id')->unique()->constrained('equipamentos')->cascadeOnDelete();
            $table->string('sistema_operacional', 60);
            $table->string('processador', 100);
            $table->string('placa_video', 100)->nullable();
            $table->unsignedSmallInteger('memoria_gb');
            $table->string('memoria_tipo', 10)->nullable();
            $table->string('memoria_formato', 10)->nullable();
            $table->unsignedInteger('armazenamento_gb');
            $table->string('armazenamento_tipo', 15);
            $table->string('mac_ethernet', 17)->nullable()->unique();
            $table->boolean('possui_wifi')->default(false);
            $table->string('mac_wifi', 17)->nullable()->unique();
            $table->json('portas_video')->nullable();
            $table->string('outras_portas', 150)->nullable();
            $table->string('anydesk_id', 20)->nullable();
            $table->timestamp('criado_em')->nullable();
            $table->timestamp('atualizado_em')->nullable();
        });

        Schema::create('monitores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipamento_id')->unique()->constrained('equipamentos')->cascadeOnDelete();
            $table->decimal('polegadas', 4, 1);
            $table->string('tipo_tela', 10)->nullable();
            $table->json('portas_video')->nullable();
            $table->timestamp('criado_em')->nullable();
            $table->timestamp('atualizado_em')->nullable();
        });

        Schema::create('impressoras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipamento_id')->unique()->constrained('equipamentos')->cascadeOnDelete();
            $table->string('tecnologia', 20);
            $table->json('conexoes');
            $table->string('mac', 17)->nullable()->unique();
            $table->timestamp('criado_em')->nullable();
            $table->timestamp('atualizado_em')->nullable();
        });

        Schema::create('dispositivos_moveis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipamento_id')->unique()->constrained('equipamentos')->cascadeOnDelete();
            $table->string('imei_1', 15)->nullable()->unique();
            $table->string('imei_2', 15)->nullable()->unique();
            $table->string('mac', 17)->nullable()->unique();
            $table->timestamp('criado_em')->nullable();
            $table->timestamp('atualizado_em')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispositivos_moveis');
        Schema::dropIfExists('impressoras');
        Schema::dropIfExists('monitores');
        Schema::dropIfExists('computadores');
    }
};
