<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Ocorrências de equipamento e termo de troca (issue #10).
 *
 * - equipamentos.status ganha "baixado" (substituído na troca pelo fornecedor, fora do parque);
 * - movimentacoes ganha o tipo "troca" e o tipo da troca (interna ou pelo fornecedor);
 * - no item entregue na troca, substitui_item_id aponta o item do empréstimo que ele substituiu;
 * - motivo "troca_fornecedor" para o item devolvido nessa troca;
 * - tabela ocorrencias.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE equipamentos MODIFY status
            ENUM('em_uso', 'defeituoso', 'descartado', 'disponivel', 'em_manutencao', 'baixado') NOT NULL DEFAULT 'disponivel'");

        DB::statement("ALTER TABLE movimentacoes MODIFY tipo_movimentacao
            ENUM('responsabilidade', 'devolucao', 'troca') NOT NULL DEFAULT 'responsabilidade'");

        Schema::table('movimentacoes', function (Blueprint $table) {
            $table->enum('tipo_troca', ['interna', 'fornecedor'])->nullable()->after('tipo_movimentacao');
        });

        DB::statement("ALTER TABLE movimentacao_equipamentos MODIFY motivo_devolucao
            ENUM('manutencao', 'defeito', 'quebra', 'devolucao', 'cancelada', 'troca_fornecedor') NOT NULL DEFAULT 'devolucao'");

        Schema::table('movimentacao_equipamentos', function (Blueprint $table) {
            $table->foreignId('substitui_item_id')->nullable()->after('devolucao_movimentacao_id')
                ->constrained('movimentacao_equipamentos')->nullOnDelete();
        });

        Schema::create('ocorrencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipamento_id')->constrained('equipamentos')->cascadeOnDelete();
            // Último usuário do equipamento quando o problema aconteceu.
            $table->foreignId('funcionario_id')->nullable()->constrained('funcionarios')->nullOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->foreignId('troca_movimentacao_id')->nullable()->constrained('movimentacoes')->nullOnDelete();

            $table->date('reportado_em');
            $table->date('data_problema')->nullable();
            $table->string('problema', 255);
            $table->date('previsao_em')->nullable();
            $table->date('liberado_em')->nullable();
            $table->text('solucao')->nullable();
            $table->string('canal', 30)->nullable();
            $table->string('protocolo', 50)->nullable();
            $table->decimal('valor_cobrado', 10, 2)->nullable();
            $table->text('observacao')->nullable();

            // A ocorrência colocou o equipamento em manutenção (e o libera ao ser resolvida).
            $table->boolean('alterou_status')->default(false);

            $table->timestamp('criado_em')->nullable();
            $table->timestamp('atualizado_em')->nullable();
            $table->softDeletes('apagado_em');

            $table->index(['equipamento_id', 'liberado_em']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ocorrencias');

        if (Schema::hasColumn('movimentacao_equipamentos', 'substitui_item_id')) {
            Schema::table('movimentacao_equipamentos', function (Blueprint $table) {
                $table->dropConstrainedForeignId('substitui_item_id');
            });
        }

        DB::statement("UPDATE movimentacao_equipamentos SET motivo_devolucao = 'devolucao' WHERE motivo_devolucao = 'troca_fornecedor'");
        DB::statement("ALTER TABLE movimentacao_equipamentos MODIFY motivo_devolucao
            ENUM('manutencao', 'defeito', 'quebra', 'devolucao', 'cancelada') NOT NULL DEFAULT 'devolucao'");

        if (Schema::hasColumn('movimentacoes', 'tipo_troca')) {
            Schema::table('movimentacoes', function (Blueprint $table) {
                $table->dropColumn('tipo_troca');
            });
        }

        // Trocas viram termos de responsabilidade (o item entregue continua sendo um empréstimo).
        DB::statement("UPDATE movimentacoes SET tipo_movimentacao = 'responsabilidade' WHERE tipo_movimentacao = 'troca'");
        DB::statement("ALTER TABLE movimentacoes MODIFY tipo_movimentacao
            ENUM('responsabilidade', 'devolucao') NOT NULL DEFAULT 'responsabilidade'");

        DB::statement("UPDATE equipamentos SET status = 'descartado' WHERE status = 'baixado'");
        DB::statement("ALTER TABLE equipamentos MODIFY status
            ENUM('em_uso', 'defeituoso', 'descartado', 'disponivel', 'em_manutencao') NOT NULL DEFAULT 'disponivel'");
    }
};
