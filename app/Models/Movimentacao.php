<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movimentacao extends Model
{
    use SoftDeletes;

    protected $table = 'movimentacoes';

    public $timestamps = true;
    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';
    const DELETED_AT = 'apagado_em';

    protected $fillable =
    [
        'setor_id',
        'funcionario_id',
        'observacao',
        'termo_responsabilidade',
        'status'
    ];

    protected $attributes = [
        'status' => 'pendente'
    ];

    public function setor()
    {
        return $this->belongsTo(Setor::class);
    }

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class);
    }

    public function equipamentos()
    {
        return $this->belongsToMany(Equipamento::class, 'movimentacao_equipamentos')
            ->using(MovimentacaoEquipamento::class)
            ->withPivot([
                'termo_devolucao',
                'observacao',
                'motivo_devolucao',
                'devolvido_em',
            ])
            ->withTimestamps();
    }
}
