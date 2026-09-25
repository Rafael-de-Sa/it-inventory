<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Campos comuns a todos os equipamentos. Fabricante e modelo são obrigatórios no formulário,
     * mas ficam anuláveis no banco para os registros cadastrados antes da versão 2.0.
     * A descrição deixa de ser obrigatória (vira observação).
     */
    public function up(): void
    {
        Schema::table('equipamentos', function (Blueprint $table) {
            $table->string('fabricante', 60)->nullable()->after('tipo_equipamento_id');
            $table->string('modelo', 80)->nullable()->after('fabricante');
            $table->string('identificacao', 50)->nullable()->unique()->after('modelo');
            $table->string('nota_fiscal', 9)->nullable()->after('valor_compra');
            $table->string('chave_acesso_nf', 44)->nullable()->after('nota_fiscal');
            $table->text('descricao')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Tolerante a estado parcial: o MySQL não desfaz DDL quando um passo falha no meio.
        $colunas = array_values(array_filter(
            ['fabricante', 'modelo', 'identificacao', 'nota_fiscal', 'chave_acesso_nf'],
            fn (string $coluna) => Schema::hasColumn('equipamentos', $coluna),
        ));

        Schema::table('equipamentos', function (Blueprint $table) use ($colunas) {
            if (Schema::hasIndex('equipamentos', ['identificacao'], 'unique')) {
                $table->dropUnique(['identificacao']);
            }

            if ($colunas !== []) {
                $table->dropColumn($colunas);
            }
        });
    }
};
