<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * Evento da linha do tempo de um equipamento. Criado pelo EquipamentoObserver; não deve ser editado.
 */
class EquipamentoHistorico extends Model
{
    const CREATED_AT = 'criado_em';
    const UPDATED_AT = null;

    public const EVENTOS = [
        'cadastro' => 'Cadastro',
        'emprestimo' => 'Empréstimo',
        'devolucao' => 'Devolução',
        'alteracao_status' => 'Alteração manual de status',
    ];

    protected $fillable = [
        'equipamento_id',
        'evento',
        'status_anterior',
        'status_novo',
        'movimentacao_id',
        'usuario_id',
        'observacao',
        'ocorrido_em',
    ];

    protected $casts = [
        'ocorrido_em' => 'datetime',
        'criado_em' => 'datetime',
    ];

    protected function eventoRotulo(): Attribute
    {
        return Attribute::get(fn () => self::EVENTOS[$this->evento] ?? $this->evento);
    }

    /** "Disponível → Em uso", ou só o status novo quando o anterior é desconhecido. */
    protected function transicaoStatus(): Attribute
    {
        return Attribute::get(function () {
            $rotulo = fn (?string $status) => $status ? (Equipamento::STATUS[$status] ?? $status) : null;
            $anterior = $rotulo($this->status_anterior);
            $novo = $rotulo($this->status_novo);

            return match (true) {
                $anterior && $novo => "{$anterior} → {$novo}",
                (bool) $novo => $novo,
                default => null,
            };
        });
    }

    public function equipamento()
    {
        return $this->belongsTo(Equipamento::class);
    }

    public function movimentacao()
    {
        return $this->belongsTo(Movimentacao::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
