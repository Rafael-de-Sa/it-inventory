<?php

namespace App\Services;

use App\Models\Equipamento;
use App\Models\Funcionario;
use App\Models\Movimentacao;
use App\Models\MovimentacaoEquipamento;
use App\Models\Ocorrencia;
use Illuminate\Support\Facades\Gate;

/**
 * Dados do dashboard da tela inicial (issue #11), conforme o perfil:
 * a TIC vê o parque, as ocorrências e os indicadores de manutenção; o DP vê funcionários e pendências.
 * Atalhos e números respeitam as mesmas permissões (Gates) do menu.
 */
class PainelInicial
{
    public static function paraUsuarioAtual(): ?array
    {
        return match (true) {
            Gate::allows('gerenciar-movimentacoes') => ['perfil' => 'tic', ...self::tic()],
            Gate::allows('gerenciar-funcionarios') => ['perfil' => 'dp', ...self::dp()],
            default => null,
        };
    }

    public static function tic(): array
    {
        $porStatus = Equipamento::query()
            ->selectRaw('status, COUNT(*) AS total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $disponiveisPorTipo = Equipamento::query()
            ->join('tipo_equipamentos', 'tipo_equipamentos.id', '=', 'equipamentos.tipo_equipamento_id')
            ->where('equipamentos.status', 'disponivel')
            ->where('equipamentos.ativo', true)
            ->selectRaw('tipo_equipamentos.nome AS tipo, COUNT(*) AS total')
            ->groupBy('tipo_equipamentos.nome')
            ->orderByDesc('total')
            ->pluck('total', 'tipo');

        $abertas = Ocorrencia::query()->whereNull('liberado_em');

        $custoPorEquipamento = Ocorrencia::query()
            ->selectRaw('equipamento_id, SUM(custo_manutencao) AS custo, COUNT(*) AS quantidade')
            ->whereNotNull('custo_manutencao')
            ->groupBy('equipamento_id')
            ->havingRaw('SUM(custo_manutencao) > 0')
            ->orderByDesc('custo')
            ->limit(5)
            ->get();
        $equipamentosCusto = Equipamento::withTrashed()->with('tipoEquipamento')
            ->findMany($custoPorEquipamento->pluck('equipamento_id'))->keyBy('id');

        return [
            'porStatus' => collect(Equipamento::STATUS)->map(fn ($rotulo, $status) => [
                'status' => $status, 'rotulo' => $rotulo, 'total' => (int) ($porStatus[$status] ?? 0),
            ])->values(),
            'totalEquipamentos' => (int) $porStatus->sum(),
            'disponiveisPorTipo' => $disponiveisPorTipo,
            'ocorrenciasAbertas' => (clone $abertas)->count(),
            'ocorrenciasAtrasadas' => (clone $abertas)->whereDate('previsao_em', '<', today())->count(),
            'ocorrenciasMaisAntigas' => (clone $abertas)->with('equipamento.tipoEquipamento')
                ->orderBy('reportado_em')->orderBy('id')->limit(5)->get(),
            'termosPendentes' => Movimentacao::query()->where('status', 'pendente')->count(),
            'custoAno' => (float) Ocorrencia::query()->whereYear('reportado_em', now()->year)->sum('custo_manutencao'),
            'resolucaoMediaDias' => self::resolucaoMedia(),
            'maiorCusto' => $custoPorEquipamento->map(fn ($linha) => [
                'equipamento' => $equipamentosCusto->get($linha->equipamento_id),
                'custo' => (float) $linha->custo,
                'quantidade' => (int) $linha->quantidade,
            ])->filter(fn ($linha) => $linha['equipamento']),
            'menorDisponibilidade' => self::menorDisponibilidade(),
            'ultimasMovimentacoes' => Movimentacao::query()->with('funcionario')->latest('criado_em')->latest('id')->limit(5)->get(),
            'atalhos' => self::atalhos([
                ['rotulo' => 'Termo de responsabilidade', 'icone' => 'fa-solid fa-file-signature', 'rota' => 'movimentacoes.create', 'can' => 'gerenciar-movimentacoes'],
                ['rotulo' => 'Termo de devolução', 'icone' => 'fa-solid fa-box-open', 'rota' => 'movimentacoes.devolucao.create', 'can' => 'gerenciar-movimentacoes'],
                ['rotulo' => 'Termo de troca', 'icone' => 'fa-solid fa-right-left', 'rota' => 'movimentacoes.troca.create', 'can' => 'gerenciar-movimentacoes'],
                ['rotulo' => 'Registrar ocorrência', 'icone' => 'fa-solid fa-triangle-exclamation', 'rota' => 'ocorrencias.create', 'can' => 'gerenciar-movimentacoes'],
                ['rotulo' => 'Cadastrar equipamento', 'icone' => 'fa-solid fa-computer', 'rota' => 'equipamentos.create', 'can' => 'gerenciar-cadastros'],
                ['rotulo' => 'Cadastrar funcionário', 'icone' => 'fa-solid fa-user-plus', 'rota' => 'funcionarios.create', 'can' => 'gerenciar-funcionarios'],
            ]),
        ];
    }

    public static function dp(): array
    {
        $comEquipamento = MovimentacaoEquipamento::query()
            ->whereNull('devolvido_em')
            ->whereHas('movimentacao', fn ($m) => $m->comEmprestimo()->where('status', '!=', 'cancelada'))
            ->with('movimentacao:id,funcionario_id')
            ->get()
            ->pluck('movimentacao.funcionario_id')
            ->unique()
            ->count();

        $comPendencia = Funcionario::query()
            ->with('setor')
            ->comRestricoesDesligamento()
            ->get()
            ->filter(fn (Funcionario $f) => $f->possui_equipamentos_em_uso || $f->possui_termos_responsabilidade_pendentes || $f->possui_termos_devolucao_pendentes);

        $desligadosComPendencia = $comPendencia->filter(fn (Funcionario $f) => $f->desligado_em !== null);

        return [
            'funcionariosAtivos' => Funcionario::query()->whereNull('desligado_em')->where('ativo', true)->count(),
            'funcionariosComEquipamento' => $comEquipamento,
            'termosPendentes' => Movimentacao::query()->where('status', 'pendente')->count(),
            'desligadosComPendencia' => $desligadosComPendencia->count(),
            // Desligados primeiro: são os que precisam ser encaminhados à TI.
            'pendencias' => $comPendencia->sortBy(fn (Funcionario $f) => [$f->desligado_em === null, $f->nome])->take(8)->values(),
            'atalhos' => self::atalhos([
                ['rotulo' => 'Cadastrar funcionário', 'icone' => 'fa-solid fa-user-plus', 'rota' => 'funcionarios.create', 'can' => 'gerenciar-funcionarios'],
                ['rotulo' => 'Funcionários', 'icone' => 'fa-solid fa-user-tie', 'rota' => 'funcionarios.index', 'can' => 'gerenciar-funcionarios'],
            ]),
        ];
    }

    /** Média de dias entre registro e liberação das ocorrências resolvidas nos últimos 12 meses. */
    private static function resolucaoMedia(): ?float
    {
        $media = Ocorrencia::query()
            ->whereNotNull('liberado_em')
            ->where('liberado_em', '>=', now()->subYear())
            ->selectRaw('AVG(DATEDIFF(liberado_em, reportado_em)) AS media')
            ->value('media');

        return $media === null ? null : round((float) $media, 1);
    }

    /** Equipamentos do parque que mais ficaram parados (menor disponibilidade), pela linha do tempo. */
    private static function menorDisponibilidade(int $limite = 5)
    {
        return Equipamento::query()
            ->with(['tipoEquipamento', 'historicos' => fn ($h) => $h->orderBy('ocorrido_em')->orderBy('id')])
            ->whereNotIn('status', IndicadoresManutencao::STATUS_FIM)
            ->get()
            ->map(function (Equipamento $equipamento) {
                [$parado, $total] = IndicadoresManutencao::tempos($equipamento->historicos, now());

                return [
                    'equipamento' => $equipamento,
                    'segundos_parado' => $parado,
                    'disponibilidade' => $total > 0 ? round(($total - $parado) / $total * 100, 1) : null,
                ];
            })
            ->filter(fn ($linha) => $linha['segundos_parado'] > 0 && $linha['disponibilidade'] !== null)
            ->sortBy('disponibilidade')
            ->take($limite)
            ->values();
    }

    private static function atalhos(array $atalhos): array
    {
        return array_values(array_filter($atalhos, fn ($atalho) => Gate::allows($atalho['can'])));
    }
}
