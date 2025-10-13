<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setor extends Model
{
    use SoftDeletes;

    protected $table = 'setores';
    public $timestamps = true;
    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';
    const DELETED_AT = 'apagado_em';

    protected $fillable = [
        'empresa_id',
        'nome',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'criado_em' => 'datetime',
        'atualizado_em' => 'datetime',
        'apagado_em' => 'datetime'
    ];

    protected $attributes = [
        'ativo' => true
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id', 'id');
    }

    public function funcionarios()
    {
        return $this->hasMany(Funcionario::class);
    }

    public function movimentacoes()
    {
        return $this->hasMany(Movimentacao::class);
    }
}
