<?php

namespace App\Models;

use App\Http\Requests\Movimentacoes\UploadTermoResponsabilidadeRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Movimentacao extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'movimentacoes';

    public $timestamps = true;
    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';
    const DELETED_AT = 'apagado_em';

    public const TIPO_RESPONSABILIDADE = 'responsabilidade';
    public const TIPO_DEVOLUCAO = 'devolucao';
    public const TIPO_TROCA = 'troca';

    public const TIPOS = [
        self::TIPO_RESPONSABILIDADE => 'Responsabilidade',
        self::TIPO_DEVOLUCAO => 'Devolução',
        self::TIPO_TROCA => 'Troca',
    ];

    /**
     * Tipos cujos itens são empréstimos ao funcionário (até serem devolvidos): o termo de responsabilidade
     * e o termo de troca, que entrega o equipamento substituto.
     */
    public const TIPOS_COM_EMPRESTIMO = [self::TIPO_RESPONSABILIDADE, self::TIPO_TROCA];

    /** Tipos de troca: interna (o antigo volta para a TI) ou pelo fornecedor (o antigo recebe baixa). */
    public const TIPOS_TROCA = [
        'interna' => 'Troca interna',
        'fornecedor' => 'Troca pelo fornecedor',
    ];

    /** Status válidos (enum da migration) e seus rótulos. */
    public const STATUS = [
        'pendente' => 'Pendente',
        'concluida' => 'Concluída',
        'encerrada' => 'Encerrada',
        'cancelada' => 'Cancelada',
    ];

    protected $fillable =
    [
        'setor_id',
        'funcionario_id',
        'observacao',
        'termo_responsabilidade',
        'status',
        'tipo_movimentacao',
        'tipo_troca',
    ];

    protected $attributes = [
        'status' => 'pendente',
        'tipo_movimentacao' => self::TIPO_RESPONSABILIDADE
    ];

    protected $casts = [
        'criado_em' => 'datetime',
        'atualizado_em' => 'datetime',
    ];

    protected function tipoRotulo(): Attribute
    {
        return Attribute::get(fn () => self::TIPOS[$this->tipo_movimentacao] ?? (string) $this->tipo_movimentacao);
    }

    protected function tipoTrocaRotulo(): Attribute
    {
        return Attribute::get(fn () => self::TIPOS_TROCA[$this->tipo_troca] ?? null);
    }

    protected function statusRotulo(): Attribute
    {
        return Attribute::get(fn () => self::STATUS[$this->status] ?? (string) $this->status);
    }

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
                'substitui_item_id',
            ])
            ->withTimestamps();
    }

    /** Ocorrências resolvidas por esta troca. */
    public function ocorrencias()
    {
        return $this->hasMany(Ocorrencia::class, 'troca_movimentacao_id');
    }

    /** Itens de empréstimo devolvidos por esta movimentação (devolução ou troca). */
    public function itensDevolvidosAqui()
    {
        return $this->hasMany(MovimentacaoEquipamento::class, 'devolucao_movimentacao_id');
    }

    public function scopeResponsabilidades($query)
    {
        return $query->where('tipo_movimentacao', self::TIPO_RESPONSABILIDADE);
    }

    /** Termos de responsabilidade e de troca (os que entregam equipamentos ao funcionário). */
    public function scopeComEmprestimo($query)
    {
        return $query->whereIn('tipo_movimentacao', self::TIPOS_COM_EMPRESTIMO);
    }

    public function entregaEquipamentos(): bool
    {
        return in_array($this->tipo_movimentacao, self::TIPOS_COM_EMPRESTIMO, true);
    }

    /** Encerra o termo quando todos os seus itens já foram devolvidos (termos cancelados não mudam). */
    public function encerrarSeTudoDevolvido(): void
    {
        $existemItensEmAberto = MovimentacaoEquipamento::query()
            ->where('movimentacao_id', $this->id)
            ->whereNull('devolvido_em')
            ->exists();

        if (! $existemItensEmAberto && $this->status !== 'cancelada') {
            $this->update(['status' => 'encerrada']);
        }
    }

    public function scopeDevolucoes($query)
    {
        return $query->where('tipo_movimentacao', self::TIPO_DEVOLUCAO);
    }
}
