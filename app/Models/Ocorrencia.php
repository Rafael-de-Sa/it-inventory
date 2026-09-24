<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Problema reportado em um equipamento (erro, defeito, bloqueio...), do registro até a liberação pela TI.
 *
 * Status do equipamento: fora de uso (com a TI, "Disponível") a ocorrência o coloca "Em manutenção" e o
 * devolve para "Disponível" ao ser liberada. Com um funcionário ("Em uso") o status não muda: para tirar
 * o equipamento dele, registra-se uma devolução ou uma troca.
 */
class Ocorrencia extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';
    const DELETED_AT = 'apagado_em';

    /** Sugestões do campo "canal" (onde o chamado foi aberto); o campo aceita outros valores. */
    public const CANAIS_SUGERIDOS = ['GLPI', 'PagBank', 'Cielo', 'RJ'];

    protected $fillable = [
        'equipamento_id',
        'funcionario_id',
        'usuario_id',
        'troca_movimentacao_id',
        'reportado_em',
        'data_problema',
        'problema',
        'previsao_em',
        'liberado_em',
        'solucao',
        'canal',
        'protocolo',
        'valor_cobrado',
        'observacao',
    ];

    protected $casts = [
        'reportado_em' => 'date',
        'data_problema' => 'date',
        'previsao_em' => 'date',
        'liberado_em' => 'date',
        'valor_cobrado' => 'decimal:2',
        'alterou_status' => 'boolean',
        'criado_em' => 'datetime',
        'atualizado_em' => 'datetime',
    ];

    public function estaAberta(): bool
    {
        return $this->liberado_em === null;
    }

    protected function situacaoRotulo(): Attribute
    {
        return Attribute::get(fn () => $this->estaAberta() ? 'Aberta' : 'Resolvida');
    }

    /** "GLPI 12167", só o protocolo ou só o canal. */
    protected function chamado(): Attribute
    {
        return Attribute::get(fn () => trim(($this->canal ?? '') . ' ' . ($this->protocolo ?? '')) ?: null);
    }

    protected function valorCobradoFormatado(): Attribute
    {
        return Attribute::get(fn () => filled($this->valor_cobrado)
            ? 'R$ ' . number_format((float) $this->valor_cobrado, 2, ',', '.')
            : null);
    }

    /**
     * Ao registrar a ocorrência de um equipamento disponível (com a TI), ele vai para "Em manutenção".
     * Não se aplica a ocorrências registradas já resolvidas.
     */
    public function colocarEquipamentoEmManutencao(): void
    {
        $equipamento = $this->equipamento;

        if (! $this->estaAberta() || $equipamento->status !== 'disponivel' || $equipamento->emprestimoEmAberto()) {
            return;
        }

        $equipamento->comHistorico('ocorrencia', null, "Ocorrência #{$this->id}: {$this->problema}")
            ->update(['status' => 'em_manutencao']);

        $this->forceFill(['alterou_status' => true])->saveQuietly();
    }

    /** Na liberação, devolve para "Disponível" o equipamento que a própria ocorrência colocou em manutenção. */
    public function liberarEquipamento(): void
    {
        $equipamento = $this->equipamento;

        if (! $this->alterou_status || $equipamento->status !== 'em_manutencao' || $equipamento->emprestimoEmAberto()) {
            return;
        }

        $equipamento->comHistorico('ocorrencia', null, "Ocorrência #{$this->id} liberada" . ($this->solucao ? ": {$this->solucao}" : '.'))
            ->update(['status' => 'disponivel']);
    }

    public function equipamento()
    {
        return $this->belongsTo(Equipamento::class)->withTrashed();
    }

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class)->withTrashed();
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function troca()
    {
        return $this->belongsTo(Movimentacao::class, 'troca_movimentacao_id');
    }

    /** Filtro da listagem: "abertas", "resolvidas" ou todas. */
    public function scopeSituacao($query, ?string $situacao)
    {
        return match ($situacao) {
            'abertas' => $query->whereNull('liberado_em'),
            'resolvidas' => $query->whereNotNull('liberado_em'),
            default => $query,
        };
    }
}
