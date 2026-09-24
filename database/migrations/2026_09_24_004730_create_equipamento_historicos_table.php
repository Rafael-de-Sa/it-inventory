<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const OBSERVACAO_RECONSTRUIDO = 'Registro reconstruído a partir dos dados existentes.';

    /**
     * Linha do tempo de eventos de cada equipamento (cadastro, empréstimo, devolução e
     * alteração manual de status). Alimentada pelo EquipamentoObserver.
     */
    public function up(): void
    {
        Schema::create('equipamento_historicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipamento_id')->constrained('equipamentos')->cascadeOnDelete();
            $table->string('evento', 30);
            $table->string('status_anterior', 20)->nullable();
            $table->string('status_novo', 20)->nullable();
            $table->foreignId('movimentacao_id')->nullable()->constrained('movimentacoes')->nullOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->text('observacao')->nullable();
            $table->timestamp('ocorrido_em');
            $table->timestamp('criado_em')->nullable();

            $table->index(['equipamento_id', 'ocorrido_em']);
        });

        $this->reconstruirHistoricoExistente();
    }

    public function down(): void
    {
        Schema::dropIfExists('equipamento_historicos');
    }

    /**
     * Gera os eventos que já podem ser deduzidos do banco: cadastro de cada equipamento,
     * empréstimos (itens de responsabilidade) e devoluções. Alterações manuais de status
     * anteriores a esta tabela não foram registradas e não podem ser recuperadas.
     */
    private function reconstruirHistoricoExistente(): void
    {
        $agora = now();
        $eventos = [];

        foreach (DB::table('equipamentos')->get(['id', 'criado_em']) as $equipamento) {
            $eventos[] = [
                'equipamento_id' => $equipamento->id,
                'evento' => 'cadastro',
                'status_anterior' => null,
                'status_novo' => null,
                'movimentacao_id' => null,
                'observacao' => self::OBSERVACAO_RECONSTRUIDO,
                'ocorrido_em' => $equipamento->criado_em ?? $agora,
            ];
        }

        $itensResponsabilidade = DB::table('movimentacao_equipamentos as item')
            ->join('movimentacoes as mov', 'mov.id', '=', 'item.movimentacao_id')
            ->leftJoin('movimentacoes as dev', 'dev.id', '=', 'item.devolucao_movimentacao_id')
            ->where('mov.tipo_movimentacao', 'responsabilidade')
            ->where('mov.status', '!=', 'cancelada')
            ->get([
                'item.equipamento_id', 'item.movimentacao_id', 'item.devolucao_movimentacao_id',
                'item.devolvido_em', 'item.motivo_devolucao', 'item.observacao', 'item.criado_em',
                'mov.criado_em as emprestado_em', 'dev.criado_em as devolucao_criada_em',
            ]);

        foreach ($itensResponsabilidade as $item) {
            $eventos[] = [
                'equipamento_id' => $item->equipamento_id,
                'evento' => 'emprestimo',
                'status_anterior' => null,
                'status_novo' => 'em_uso',
                'movimentacao_id' => $item->movimentacao_id,
                'observacao' => self::OBSERVACAO_RECONSTRUIDO,
                'ocorrido_em' => $item->criado_em ?? $item->emprestado_em,
            ];

            if ($item->devolvido_em) {
                $eventos[] = [
                    'equipamento_id' => $item->equipamento_id,
                    'evento' => 'devolucao',
                    'status_anterior' => 'em_uso',
                    'status_novo' => match ($item->motivo_devolucao) {
                        'manutencao' => 'em_manutencao',
                        'defeito', 'quebra' => 'defeituoso',
                        default => 'disponivel',
                    },
                    'movimentacao_id' => $item->devolucao_movimentacao_id,
                    'observacao' => trim(self::OBSERVACAO_RECONSTRUIDO . ' ' . ($item->observacao ?? '')),
                    'ocorrido_em' => $item->devolucao_criada_em ?? $item->devolvido_em,
                ];
            }
        }

        foreach (array_chunk($eventos, 500) as $lote) {
            DB::table('equipamento_historicos')->insert(array_map(
                fn (array $evento) => $evento + ['usuario_id' => null, 'criado_em' => $agora],
                $lote
            ));
        }
    }
};
