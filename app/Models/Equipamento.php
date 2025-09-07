<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipamento extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';

    protected $fillable = [
        'tipo_equipamento_id',
        'data_compra',
        'valor_compra',
        'status',
        'ativo',
        'descricao',
        'patrimonio',
        'numero_serie'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'data_compra' => 'date',
        'valor_compra' => 'decimal:2',
        'criado_em' => 'datetime',
        'atualizado_em' => 'datetime',
        'apagado_em' => 'datetime'
    ];

    protected $attributes = [
        'ativo' => true,
        'status' => 'disponivel'
    ];

    public function tipoEquipamento()
    {
        return $this->belongsTo(TipoEquipamento::class);
    }

    public function movimentacoes()
    {
        return $this->hasMany(MovimentacaoEquipamento::class);
    }
}
