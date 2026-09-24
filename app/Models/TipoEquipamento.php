<?php

namespace App\Models;

use App\Enums\CategoriaEquipamento;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoEquipamento extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;
    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';
    const DELETED_AT = 'apagado_em';

    protected $fillable = [
        'nome',
        'categoria',
        'ativo'
    ];

    protected $casts = [
        'categoria' => CategoriaEquipamento::class,
        'ativo' => 'boolean',
        'criado_em' => 'datetime',
        'atualizado_em' => 'datetime',
        'apagado_em' => 'datetime'
    ];

    protected $attributes = [
        'categoria' => 'generico',
        'ativo' => true
    ];

    public function equipamentos()
    {
        return $this->hasMany(Equipamento::class);
    }
}
