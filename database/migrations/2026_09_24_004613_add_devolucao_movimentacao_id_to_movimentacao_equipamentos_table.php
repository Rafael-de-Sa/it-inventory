<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Liga o item do termo de responsabilidade à movimentação de devolução que o encerrou.
     * Só é preenchida nos itens de responsabilidade já devolvidos.
     */
    public function up(): void
    {
        Schema::table('movimentacao_equipamentos', function (Blueprint $table) {
            $table->foreignId('devolucao_movimentacao_id')
                ->nullable()
                ->after('movimentacao_id')
                ->constrained('movimentacoes')
                ->nullOnDelete();
        });

        $this->vincularDevolucoesExistentes();
    }

    public function down(): void
    {
        Schema::table('movimentacao_equipamentos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('devolucao_movimentacao_id');
        });
    }

    /**
     * Para os registros anteriores a esta coluna, encontra a devolução correspondente:
     * mesmo equipamento, mesmo funcionário, mesma data de devolução e criada após o empréstimo.
     * Cada item de devolução é usado uma única vez.
     */
    private function vincularDevolucoesExistentes(): void
    {
        $itensResponsabilidade = DB::table('movimentacao_equipamentos as item')
            ->join('movimentacoes as mov', 'mov.id', '=', 'item.movimentacao_id')
            ->where('mov.tipo_movimentacao', 'responsabilidade')
            ->whereNotNull('item.devolvido_em')
            ->whereNull('item.devolucao_movimentacao_id')
            ->orderBy('item.criado_em')
            ->orderBy('item.id')
            ->get(['item.id', 'item.equipamento_id', 'item.devolvido_em', 'item.criado_em', 'mov.funcionario_id']);

        $itensDevolucaoUsados = [];

        foreach ($itensResponsabilidade as $itemResponsabilidade) {
            $itemDevolucao = DB::table('movimentacao_equipamentos as item')
                ->join('movimentacoes as mov', 'mov.id', '=', 'item.movimentacao_id')
                ->where('mov.tipo_movimentacao', 'devolucao')
                ->where('mov.funcionario_id', $itemResponsabilidade->funcionario_id)
                ->where('item.equipamento_id', $itemResponsabilidade->equipamento_id)
                ->where('item.devolvido_em', $itemResponsabilidade->devolvido_em)
                ->where('mov.criado_em', '>=', $itemResponsabilidade->criado_em)
                ->whereNotIn('item.id', $itensDevolucaoUsados ?: [0])
                ->orderBy('mov.criado_em')
                ->orderBy('item.id')
                ->first(['item.id', 'item.movimentacao_id']);

            if (! $itemDevolucao) {
                continue;
            }

            $itensDevolucaoUsados[] = $itemDevolucao->id;

            DB::table('movimentacao_equipamentos')
                ->where('id', $itemResponsabilidade->id)
                ->update(['devolucao_movimentacao_id' => $itemDevolucao->movimentacao_id]);
        }
    }
};
