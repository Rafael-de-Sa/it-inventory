<?php

namespace App\Models;

use App\Observers\EquipamentoObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(EquipamentoObserver::class)]
class Equipamento extends Model
{
    use SoftDeletes;

    /** Contexto do próximo evento de histórico (ver comHistorico()); não é persistido. */
    private array $contextoHistorico = [];

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

    /**
     * Identifica o motivo da próxima mudança de status para a linha do tempo:
     *   $equipamento->comHistorico('emprestimo', $movimentacao)->update(['status' => 'em_uso']);
     */
    public function comHistorico(string $evento, ?Movimentacao $movimentacao = null, ?string $observacao = null): static
    {
        $this->contextoHistorico = compact('evento', 'movimentacao', 'observacao');

        return $this;
    }

    /** Usado pelo EquipamentoObserver: devolve o contexto e o limpa para o próximo save. */
    public function consumirContextoHistorico(): array
    {
        $contexto = $this->contextoHistorico;
        $this->contextoHistorico = [];

        return $contexto;
    }

    public function historicos()
    {
        return $this->hasMany(EquipamentoHistorico::class);
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
