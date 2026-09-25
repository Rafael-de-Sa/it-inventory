<?php

namespace App\Models;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Funcionario extends Model
{
    use HasFactory, SoftDeletes;

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
        'admitido_em',
        'desligado_em',
        'ativo',
        'telefone',
        'terceirizado'
    ];

    protected $casts = [
        'telefones' => 'array',
        'admitido_em' => 'date',
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

    protected function nomeCompleto(): Attribute
    {
        return Attribute::get(fn () => trim($this->nome . ' ' . $this->sobrenome));
    }

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
        return $this->restricaoPreCarregada('possui_equipamentos_em_uso')
            ?? self::consultaEquipamentosEmUso($this->doFuncionario())->exists();
    }

    public function possuiTermosResponsabilidadePendentes(): bool
    {
        return $this->restricaoPreCarregada('possui_termos_responsabilidade_pendentes')
            ?? self::consultaTermosResponsabilidadePendentes($this->doFuncionario())->exists();
    }

    public function possuiTermosDevolucaoPendentes(): bool
    {
        return $this->restricaoPreCarregada('possui_termos_devolucao_pendentes')
            ?? self::consultaTermosDevolucaoPendentes($this->doFuncionario())->exists();
    }

    /**
     * Pré-carrega as restrições de desligamento como subconsultas, evitando N+1 em listagens.
     * Deve ser aplicado depois de qualquer select(), pois usa addSelect().
     */
    public function scopeComRestricoesDesligamento(Builder $query): void
    {
        $daLinhaAtual = fn (Builder $movimentacoes) => $movimentacoes
            ->whereColumn('movimentacoes.funcionario_id', 'funcionarios.id');

        if (is_null($query->getQuery()->columns)) {
            $query->select('funcionarios.*');
        }

        $query->addSelect([
            'possui_equipamentos_em_uso' => self::consultaEquipamentosEmUso($daLinhaAtual)
                ->selectRaw('1')->limit(1),
            'possui_termos_responsabilidade_pendentes' => self::consultaTermosResponsabilidadePendentes($daLinhaAtual)
                ->selectRaw('1')->limit(1),
            'possui_termos_devolucao_pendentes' => self::consultaTermosDevolucaoPendentes($daLinhaAtual)
                ->selectRaw('1')->limit(1),
        ]);
    }

    /**
     * Valor carregado por scopeComRestricoesDesligamento(), ou null quando não foi pré-carregado.
     */
    private function restricaoPreCarregada(string $atributo): ?bool
    {
        return array_key_exists($atributo, $this->attributes) ? (bool) $this->attributes[$atributo] : null;
    }

    /**
     * Restringe uma consulta de movimentações a este funcionário.
     */
    private function doFuncionario(): Closure
    {
        return fn (Builder $movimentacoes) => $movimentacoes->where('funcionario_id', $this->id);
    }

    private static function consultaEquipamentosEmUso(Closure $doFuncionario): Builder
    {
        return MovimentacaoEquipamento::query()
            ->whereNull('devolvido_em')
            ->whereHas('movimentacao', fn (Builder $movimentacoes) => $doFuncionario($movimentacoes)
                ->comEmprestimo()
                ->where('status', '!=', 'cancelada'));
    }

    private static function consultaTermosResponsabilidadePendentes(Closure $doFuncionario): Builder
    {
        return $doFuncionario(Movimentacao::query())
            ->comEmprestimo()
            ->where('status', '!=', 'cancelada')
            ->whereNull('termo_responsabilidade');
    }

    private static function consultaTermosDevolucaoPendentes(Closure $doFuncionario): Builder
    {
        return $doFuncionario(Movimentacao::query())
            ->where('tipo_movimentacao', Movimentacao::TIPO_DEVOLUCAO)
            ->where('status', '!=', 'cancelada')
            ->whereNull('termo_devolucao');
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
