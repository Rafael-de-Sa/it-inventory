<?php

namespace App\Services;

use App\Models\Equipamento;
use App\Models\EquipamentoHistorico;
use App\Models\Ocorrencia;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

/**
 * Indicadores de manutenção de um equipamento (issue #11).
 *
 * - Custo: soma do custo das ocorrências e percentual sobre o valor de compra.
 * - Tempo parado: períodos em "Em manutenção" ou "Defeituoso", pela linha do tempo de status
 *   (cobre ocorrência, devolução por manutenção/defeito e troca). O período termina na baixa/descarte.
 * - Disponibilidade (uptime): tempo fora dessas situações ÷ tempo desde o primeiro evento.
 * - Tempo médio de resolução: média de "reportado em" → "liberado em" das ocorrências resolvidas.
 */
class IndicadoresManutencao
{
    /** Situações em que o equipamento não pode ser usado. */
    public const STATUS_PARADO = ['em_manutencao', 'defeituoso'];

    /** Situações que encerram a vida útil (o tempo depois delas não conta). */
    public const STATUS_FIM = ['baixado', 'descartado'];

    /**
     * @param  Collection<int, EquipamentoHistorico>|null  $historicos  já carregados (ex.: no dashboard, para vários equipamentos)
     * @param  Collection<int, Ocorrencia>|null  $ocorrencias
     * @return array{ocorrencias: int, abertas: int, custo: float, percentual_custo: ?float, segundos_parado: int,
     *               disponibilidade: ?float, resolucao_media_dias: ?float}
     */
    public static function doEquipamento(
        Equipamento $equipamento,
        ?Collection $historicos = null,
        ?Collection $ocorrencias = null,
        ?CarbonInterface $agora = null,
    ): array {
        $historicos ??= $equipamento->historicos()->orderBy('ocorrido_em')->orderBy('id')->get();
        $ocorrencias ??= $equipamento->ocorrencias()->get();

        $custo = (float) $ocorrencias->sum(fn (Ocorrencia $o) => (float) $o->custo_manutencao);
        $valorCompra = (float) $equipamento->valor_compra;
        [$segundosParado, $segundosTotais] = self::tempos($historicos, $agora ?? now());

        $resolvidas = $ocorrencias->filter(fn (Ocorrencia $o) => $o->liberado_em !== null);

        return [
            'ocorrencias' => $ocorrencias->count(),
            'abertas' => $ocorrencias->count() - $resolvidas->count(),
            'custo' => $custo,
            'percentual_custo' => $valorCompra > 0 ? round($custo / $valorCompra * 100, 1) : null,
            'segundos_parado' => $segundosParado,
            'disponibilidade' => $segundosTotais > 0 ? round(($segundosTotais - $segundosParado) / $segundosTotais * 100, 1) : null,
            'resolucao_media_dias' => $resolvidas->isNotEmpty()
                ? round($resolvidas->avg(fn (Ocorrencia $o) => $o->reportado_em->diffInDays($o->liberado_em)), 1)
                : null,
        ];
    }

    /**
     * Segundos parado e segundos totais, percorrendo a linha do tempo em ordem.
     *
     * @return array{0: int, 1: int}
     */
    public static function tempos(Collection $historicos, CarbonInterface $agora): array
    {
        $inicio = $historicos->first()?->ocorrido_em;
        if (! $inicio) {
            return [0, 0];
        }

        $parado = 0;
        $status = null;
        $desde = $inicio;
        $fim = $agora;

        foreach ($historicos as $evento) {
            if (in_array($status, self::STATUS_PARADO, true)) {
                $parado += max(0, $desde->diffInSeconds($evento->ocorrido_em));
            }

            $status = $evento->status_novo ?? $status;
            $desde = $evento->ocorrido_em;

            if (in_array($status, self::STATUS_FIM, true)) {
                $fim = $evento->ocorrido_em;
                break;
            }
        }

        if (in_array($status, self::STATUS_PARADO, true)) {
            $parado += max(0, $desde->diffInSeconds($fim));
        }

        return [(int) $parado, (int) max(0, $inicio->diffInSeconds($fim))];
    }

    /** "3,5 dias", "5 h" ou "—". */
    public static function duracao(int $segundos): string
    {
        if ($segundos <= 0) {
            return '—';
        }

        $dias = $segundos / 86400;

        return $dias >= 1
            ? str_replace('.', ',', (string) round($dias, 1)) . ' ' . ($dias < 2 ? 'dia' : 'dias')
            : max(1, (int) round($segundos / 3600)) . ' h';
    }

    public static function percentual(?float $valor): string
    {
        return $valor === null ? '—' : str_replace('.', ',', (string) $valor) . '%';
    }
}
