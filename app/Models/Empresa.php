<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Support\Mask;

class Empresa extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';
    const DELETED_AT = 'apagado_em';

    protected $fillable = [
        'nome_fantasia',
        'razao_social',
        'cnpj',
        'rua',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'email',
        'ativo',
        'telefone'
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

    public function setores()
    {
        return $this->hasMany(Setor::class);
    }

    public function getCnpjMaskedAttribute(): string
    {
        return Mask::cnpj($this->cnpj);
    }
    public function getCepMaskedAttribute(): string
    {
        return Mask::cep($this->cep);
    }
    public function getTelefoneMaskedAttribute(): string
    {
        return Mask::telefone($this->telefone);
    }
}
