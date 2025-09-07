<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Funcionario extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';

    protected $fillable = [
        'setor_id',
        'nome',
        'sobrenome',
        'cpf',
        'matricula',
        'desligado_em',
        'ativo',
        'telefones',
        'terceirizado'
    ];

    protected $casts = [
        'telefones ' => 'array',
        'desligado_em' => 'date',
        'ativo' => 'boolean',
        'terceirizado' => 'boolean',
        'criado_em' => 'datetime',
        'atualizado_em' => 'datetime',
        'apagado_em' => 'datetime'
    ];

    protected $attributes = [
        'ativo' => true,
        'terceirizado' => false
    ];

    public function setor()
    {
        return $this->belongsTo(Setor::class);
    }

    public function usuario()
    {
        return $this->hasOne(Usuario::class);
    }

    public function movimentacoes()
    {
        return $this->hasMany(Movimentacao::class);
    }
}
