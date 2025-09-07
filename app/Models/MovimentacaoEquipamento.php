<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovimentacaoEquipamento extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';

    protected $fillable = [
        'movimentacao_id',
        'equipamento_id',
        'termo_devolucao',
        'observacao',
        'motivo_devolucao',
        'devolvido_em'
    ];

    protected $casts = [
        'devolvido_em' => 'date'
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
}
