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
    const DELETED_AT = 'apagado_em';

    protected $fillable = [
        'setor_id',
        'nome',
        'sobrenome',
        'cpf',
        'matricula',
        'desligado_em',
        'ativo',
        'telefone',
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

    public function scopeAptosParaUsuario($query)
    {
        return $query
            ->whereNull('desligado_em')
            ->where('ativo', true)
            ->where('terceirizado', false)
            ->whereDoesntHave('usuario')
            ->whereNull('apagado_em');
    }

    public function jaEstaDesligado(): bool
    {
        return ! is_null($this->desligado_em);
    }

    public function possuiEquipamentosEmUso(): bool
    {
        return MovimentacaoEquipamento::query()
            ->whereNull('devolvido_em')
            ->whereHas('movimentacao', function ($consultaMovimentacao) {
                $consultaMovimentacao
                    ->where('funcionario_id', $this->id)
                    ->where('tipo_movimentacao', Movimentacao::TIPO_RESPONSABILIDADE)
                    ->where('status', '!=', 'cancelada');
            })
            ->exists();
    }

    public function possuiTermosResponsabilidadePendentes(): bool
    {
        return Movimentacao::query()
            ->where('funcionario_id', $this->id)
            ->where('tipo_movimentacao', Movimentacao::TIPO_RESPONSABILIDADE)
            ->where('status', '!=', 'cancelada')
            ->whereNull('termo_responsabilidade')
            ->exists();
    }

    public function possuiTermosDevolucaoPendentes(): bool
    {
        return Movimentacao::query()
            ->where('funcionario_id', $this->id)
            ->where('tipo_movimentacao', Movimentacao::TIPO_DEVOLUCAO)
            ->where('status', '!=', 'cancelada')
            ->whereNull('termo_devolucao')
            ->exists();
    }

    public function obterRestricoesDesligamento(): array
    {
        return [
            'ja_desligado' => $this->jaEstaDesligado(),
            'equipamentos_em_uso' => $this->possuiEquipamentosEmUso(),
            'termos_responsabilidade_pendentes' => $this->possuiTermosResponsabilidadePendentes(),
            'termos_devolucao_pendentes' => $this->possuiTermosDevolucaoPendentes(),
        ];
    }

    public function podeSerDesligado(): bool
    {
        $restricoes = $this->obterRestricoesDesligamento();

        return ! in_array(true, $restricoes, true);
    }
}
