<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Usuario extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';
    const DELETED_AT = 'apagado_em';

    protected $fillable = [
        'funcionario_id',
        'email',
        'senha',
        'ultimo_login',
        'ativo'
    ];

    protected $casts = [
        'senha' => 'hashed',
        'ultimo_login' => 'timestamp',
        'ativo' => 'boolean',
        'criado_em' => 'datetime',
        'atualizado_em' => 'datetime',
        'apagado_em' => 'datetime'
    ];

    protected $attributes = [
        'ativo' => true
    ];

    protected $hidden = ['senha'];

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class);
    }
}
