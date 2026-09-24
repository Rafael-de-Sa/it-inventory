<?php

namespace App\Models;

use App\Observers\EquipamentoObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(EquipamentoObserver::class)]
class Equipamento extends Model
{
    use HasFactory, SoftDeletes;

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

    /** Acima deste valor de compra o patrimônio (plaquinha) é obrigatório. */
    public const VALOR_MINIMO_PATRIMONIO = 1500;

    protected $fillable = [
        'tipo_equipamento_id',
        'fabricante',
        'modelo',
        'identificacao',
        'data_compra',
        'valor_compra',
        'nota_fiscal',
        'chave_acesso_nf',
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

    /** Relações das fichas técnicas, para eager loading: Equipamento::with(Equipamento::RELACOES_FICHA). */
    public const RELACOES_FICHA = ['computador', 'monitor', 'impressora', 'dispositivoMovel'];

    /**
     * Resumo da ficha técnica em uma linha, para termos e relatórios:
     * "Intel Core i5-13450HX · 16 GB DDR5 · 512 GB SSD NVMe", "21,5 pol. LED", "IMEI 354494165730091".
     */
    protected function resumoTecnico(): Attribute
    {
        return Attribute::get(function () {
            $gb = fn (?int $valor) => $valor >= 1000 && $valor % 1000 === 0 ? ($valor / 1000) . ' TB' : $valor . ' GB';
            $juntar = fn (array $partes) => implode(' · ', array_filter(array_map('trim', $partes))) ?: null;

            return match (true) {
                (bool) $this->computador => $juntar([
                    $this->computador->processador,
                    trim($gb($this->computador->memoria_gb) . ' ' . $this->computador->memoria_tipo),
                    $gb($this->computador->armazenamento_gb) . ' ' . $this->computador->armazenamento_tipo,
                ]),
                (bool) $this->monitor => $juntar([
                    str_replace('.', ',', rtrim(rtrim((string) $this->monitor->polegadas, '0'), '.')) . ' pol.',
                    (string) $this->monitor->tipo_tela,
                ]),
                (bool) $this->impressora => $juntar([
                    Impressora::TECNOLOGIAS[$this->impressora->tecnologia] ?? $this->impressora->tecnologia,
                    implode(', ', array_map(fn ($c) => Impressora::CONEXOES[$c] ?? $c, $this->impressora->conexoes ?? [])),
                ]),
                (bool) $this->dispositivoMovel => $juntar([
                    $this->dispositivoMovel->imei_1 ? 'IMEI ' . $this->dispositivoMovel->imei_1 : null,
                    $this->dispositivoMovel->imei_2 ? 'IMEI 2 ' . $this->dispositivoMovel->imei_2 : null,
                ]),
                default => null,
            };
        });
    }

    /** Chave de acesso da NF-e em blocos de 4 dígitos, como impressa no DANFE. */
    protected function chaveAcessoNfFormatada(): Attribute
    {
        return Attribute::get(fn () => $this->chave_acesso_nf ? implode(' ', str_split($this->chave_acesso_nf, 4)) : null);
    }

    /**
     * Nome para listagens, termos e relatórios: "Dell G15 5530". Equipamentos cadastrados antes
     * da versão 2.0 (sem fabricante/modelo) usam a descrição.
     */
    protected function nomeExibicao(): Attribute
    {
        return Attribute::get(function () {
            $nome = trim(($this->fabricante ?? '') . ' ' . ($this->modelo ?? ''));

            return $nome !== '' ? $nome : (string) ($this->descricao ?? '');
        });
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

    public function computador()
    {
        return $this->hasOne(Computador::class);
    }

    public function monitor()
    {
        return $this->hasOne(Monitor::class);
    }

    public function impressora()
    {
        return $this->hasOne(Impressora::class);
    }

    public function dispositivoMovel()
    {
        return $this->hasOne(DispositivoMovel::class);
    }

    public function movimentacoes()
    {
        return $this->belongsToMany(Movimentacao::class, 'movimentacao_equipamentos');
    }
}
