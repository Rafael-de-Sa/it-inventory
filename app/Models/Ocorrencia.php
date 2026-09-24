<?php

namespace App\Models;

use App\Services\RegistroDevolucao;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Problema reportado em um equipamento (erro, defeito, bloqueio...), do registro até a liberação pela TI.
 *
 * Status do equipamento:
 * - com um funcionário ("Em uso"), a ocorrência pode recolhê-lo: registra a devolução (motivo Manutenção),
 *   gerando o termo de devolução, e o equipamento fica "Em manutenção";
 * - com a TI ("Disponível"), vai direto para "Em manutenção";
 * - na liberação, o que a própria ocorrência colocou em manutenção volta para "Disponível".
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
        'devolucao_movimentacao_id',
        'reportado_em',
        'data_problema',
        'problema',
        'previsao_em',
        'liberado_em',
        'solucao',
        'canal',
        'protocolo',
        'valor_cobrado',
        'custo_manutencao',
        'fornecedor',
        'observacao',
    ];

    protected $casts = [
        'reportado_em' => 'date',
        'data_problema' => 'date',
        'previsao_em' => 'date',
        'liberado_em' => 'date',
        'valor_cobrado' => 'decimal:2',
        'custo_manutencao' => 'decimal:2',
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
        return Attribute::get(fn () => self::reais($this->valor_cobrado));
    }

    protected function custoManutencaoFormatado(): Attribute
    {
        return Attribute::get(fn () => self::reais($this->custo_manutencao));
    }

    public static function reais($valor): ?string
    {
        return filled($valor) ? 'R$ ' . number_format((float) $valor, 2, ',', '.') : null;
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

    /**
     * Recolhe o equipamento de quem está com ele: devolução com motivo Manutenção (termo de devolução pendente
     * de assinatura) e status "Em manutenção". Sem empréstimo em aberto, equivale a colocarEquipamentoEmManutencao().
     */
    public function recolherEquipamento(): void
    {
        $emprestimo = $this->equipamento->emprestimoEmAberto();

        if (! $this->estaAberta() || ! $emprestimo) {
            $this->colocarEquipamentoEmManutencao();

            return;
        }

        $funcionario = $emprestimo->movimentacao->funcionario;

        $devolucao = RegistroDevolucao::registrar(
            $funcionario->setor_id ?? $emprestimo->movimentacao->setor_id,
            $funcionario->id,
            [$this->equipamento_id => ['motivo' => 'manutencao', 'observacao' => "Ocorrência #{$this->id}: {$this->problema}"]],
            "Recolhido para manutenção (ocorrência #{$this->id}).",
        );

        $this->forceFill(['devolucao_movimentacao_id' => $devolucao->id, 'alterou_status' => true])->saveQuietly();
    }

    /**
     * Link para devolver ao funcionário o equipamento recolhido por esta ocorrência, já liberado: abre o termo de
     * responsabilidade com o funcionário e o equipamento preenchidos. Null quando não se aplica — em especial se o
     * equipamento já foi emprestado de novo depois da devolução (para ele ou para outra pessoa).
     */
    public function urlDevolverAoFuncionario(): ?string
    {
        $devolucao = $this->devolucao;
        $funcionario = $devolucao?->funcionario;
        $equipamento = $this->equipamento;

        if ($this->estaAberta() || ! $funcionario || $funcionario->desligado_em || ! $funcionario->ativo
            || $equipamento->status !== 'disponivel' || ! $equipamento->ativo) {
            return null;
        }

        $emprestadoDepois = MovimentacaoEquipamento::query()
            ->where('equipamento_id', $equipamento->id)
            ->where('movimentacao_id', '>', $devolucao->id)
            ->whereHas('movimentacao', fn ($m) => $m->comEmprestimo()->where('status', '!=', 'cancelada'))
            ->exists();

        return $emprestadoDepois ? null : route('movimentacoes.create', [
            'empresa_id' => $funcionario->setor?->empresa_id,
            'setor_id' => $funcionario->setor_id,
            'funcionario_id' => $funcionario->id,
            'equipamentos' => [$equipamento->id],
        ]);
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

    public function devolucao()
    {
        return $this->belongsTo(Movimentacao::class, 'devolucao_movimentacao_id');
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
