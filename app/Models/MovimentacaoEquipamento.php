<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovimentacaoEquipamento extends Pivot
{
    use SoftDeletes;

    protected $table = 'movimentacao_equipamentos';
    public $timestamps = true;
    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';
    const DELETED_AT = 'apagado_em';

    protected $fillable = [
        'movimentacao_id',
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

    public function movimentacao()
    {
        return $this->belongsTo(Movimentacao::class);
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
                'equipamento' => function ($queryEquipamento) {
                    $queryEquipamento->withTrashed();
                },
            ])
            ->where('equipamento_id', $equipamentoId)
            ->whereHas('movimentacao', function ($subQuery) {
                $subQuery
                    ->withTrashed()
                    ->where('tipo_movimentacao', Movimentacao::TIPO_RESPONSABILIDADE);
            })
            ->orderByDesc('criado_em');
    }
}
