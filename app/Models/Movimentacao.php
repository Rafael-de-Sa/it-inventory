<?php

namespace App\Models;

use App\Http\Requests\Movimentacoes\UploadTermoResponsabilidadeRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Movimentacao extends Model
{
    use SoftDeletes;

    protected $table = 'movimentacoes';

    public $timestamps = true;
    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';
    const DELETED_AT = 'apagado_em';

    public const TIPO_RESPONSABILIDADE = 'responsabilidade';
    public const TIPO_DEVOLUCAO = 'devolucao';

    protected $fillable =
    [
        'setor_id',
        'funcionario_id',
        'observacao',
        'termo_responsabilidade',
        'status',
        'tipo_movimentacao'
    ];

    protected $attributes = [
        'status' => 'pendente',
        'tipo_movimentacao' => self::TIPO_RESPONSABILIDADE
    ];

    protected $casts = [
        'criado_em' => 'datetime',
        'atualizado_em' => 'datetime',
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

    public function scopeResponsabilidades($query)
    {
        return $query->where('tipo_movimentacao', self::TIPO_RESPONSABILIDADE);
    }

    public function scopeDevolucoes($query)
    {
        return $query->where('tipo_movimentacao', self::TIPO_DEVOLUCAO);
    }
}
