<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipamento extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';
    const DELETED_AT = 'apagado_em';

    /** Status válidos (enum da migration) e seus rótulos. */
    public const STATUS = [
        'disponivel' => 'Disponível',
        'em_uso' => 'Em uso',
        'em_manutencao' => 'Em manutenção',
        'defeituoso' => 'Defeituoso',
        'descartado' => 'Descartado',
    ];

    /** "Em uso" só é atribuído pelas movimentações, não no cadastro manual. */
    public const STATUS_CADASTRO = ['disponivel', 'em_manutencao', 'defeituoso', 'descartado'];

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

    public function setNumeroSerieAttribute($valor): void
    {
        $this->attributes['numero_serie'] = $valor ? mb_strtoupper(trim($valor)) : null;
    }

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

    protected function statusRotulo(): Attribute
    {
        return Attribute::get(fn () => self::STATUS[$this->status] ?? (string) $this->status);
    }

    /**
     * Item de termo de responsabilidade ainda não devolvido (ignora movimentações canceladas).
     * Enquanto existir, o status do equipamento só muda via devolução.
     */
    public function emprestimoEmAberto(): ?MovimentacaoEquipamento
    {
        return MovimentacaoEquipamento::query()
            ->where('equipamento_id', $this->id)
            ->whereNull('devolvido_em')
            ->whereHas('movimentacao', fn ($movimentacoes) => $movimentacoes
                ->where('tipo_movimentacao', Movimentacao::TIPO_RESPONSABILIDADE)
                ->where('status', '!=', 'cancelada'))
            ->latest('id')
            ->first();
    }

    public function tipoEquipamento()
    {
        return $this->belongsTo(TipoEquipamento::class);
    }

    public function movimentacoes()
    {
        return $this->belongsToMany(Movimentacao::class, 'movimentacao_equipamentos');
    }
}
