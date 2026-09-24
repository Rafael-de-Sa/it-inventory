<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovimentacaoEquipamento extends Pivot
{
    use HasFactory, SoftDeletes;

    protected $table = 'movimentacao_equipamentos';
    public $timestamps = true;
    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';
    const DELETED_AT = 'apagado_em';

    /** Motivos de devolução (enum da migration) e seus rótulos. */
    public const MOTIVOS_DEVOLUCAO = [
        'devolucao' => 'Devolução',
        'manutencao' => 'Manutenção',
        'defeito' => 'Defeito',
        'quebra' => 'Quebra',
        'cancelada' => 'Movimentação cancelada',
        'troca_fornecedor' => 'Troca pelo fornecedor',
    ];

    /** Motivos que o usuário pode escolher ao registrar uma devolução ("cancelada" é de uso interno). */
    public const MOTIVOS_SELECIONAVEIS = ['manutencao', 'defeito', 'quebra', 'devolucao'];

    protected $fillable = [
        'movimentacao_id',
        'devolucao_movimentacao_id',
        'substitui_item_id',
        'equipamento_id',
        'termo_devolucao',
        'observacao',
        'motivo_devolucao',
        'devolvido_em'
    ];

    protected $casts = [
        'devolvido_em' => 'date',
        'criado_em' => 'datetime',
        'atualizado_em' => 'datetime',
        'apagado_em' => 'datetime'
    ];

    protected $attributes = [
        'motivo_devolucao' => 'devolucao'
    ];

    /** Status que o equipamento assume ao ser devolvido com o motivo informado. */
    public static function statusEquipamentoAposDevolucao(?string $motivo): string
    {
        return match ($motivo) {
            'manutencao' => 'em_manutencao',
            'defeito', 'quebra' => 'defeituoso',
            'troca_fornecedor' => 'baixado',
            default => 'disponivel',
        };
    }

    protected function motivoDevolucaoRotulo(): Attribute
    {
        return Attribute::get(fn () => self::MOTIVOS_DEVOLUCAO[$this->motivo_devolucao] ?? null);
    }

    public function movimentacao()
    {
        return $this->belongsTo(Movimentacao::class);
    }

    /** Movimentação (devolução ou troca) que encerrou este item de empréstimo. */
    public function movimentacaoDevolucao()
    {
        return $this->belongsTo(Movimentacao::class, 'devolucao_movimentacao_id');
    }

    /** No item entregue em uma troca: o item de empréstimo que ele substituiu. */
    public function itemSubstituido()
    {
        return $this->belongsTo(self::class, 'substitui_item_id');
    }

    public function equipamento()
    {
        return $this->belongsTo(Equipamento::class);
    }

    public function scopeHistoricoResponsabilidadePorEquipamento($query, int $equipamentoId): void
    {
        $query
            ->with([
                'movimentacao' => function ($queryMovimentacao) {
                    $queryMovimentacao
                        ->withTrashed()
                        ->with([
                            'funcionario' => function ($queryFuncionario) {
                                $queryFuncionario
                                    ->withTrashed()
                                    ->with([
                                        'setor' => function ($querySetor) {
                                            $querySetor
                                                ->withTrashed()
                                                ->with([
                                                    'empresa' => function ($queryEmpresa) {
                                                        $queryEmpresa->withTrashed();
                                                    },
                                                ]);
                                        },
                                    ]);
                            },
                        ]);
                },
                'movimentacaoDevolucao' => function ($queryDevolucao) {
                    $queryDevolucao->withTrashed();
                },
                'equipamento' => function ($queryEquipamento) {
                    $queryEquipamento->withTrashed();
                },
            ])
            ->where('equipamento_id', $equipamentoId)
            ->whereHas('movimentacao', function ($subQuery) {
                $subQuery
                    ->withTrashed()
                    ->whereIn('tipo_movimentacao', Movimentacao::TIPOS_COM_EMPRESTIMO);
            })
            ->orderByDesc('criado_em')
            ->orderByDesc('id');
    }
}
