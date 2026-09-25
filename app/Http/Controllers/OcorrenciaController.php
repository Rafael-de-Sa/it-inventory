<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ocorrencias\EncerrarOcorrenciaRequest;
use App\Http\Requests\Ocorrencias\StoreOcorrenciaRequest;
use App\Http\Requests\Ocorrencias\UpdateOcorrenciaRequest;
use App\Models\Equipamento;
use App\Models\Funcionario;
use App\Models\MovimentacaoEquipamento;
use App\Models\Ocorrencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Ocorrências de equipamento (issues #10 e #11): abertura (registro), acompanhamento (edição), encerramento
 * (liberação pela TI) e reabertura. A troca vinculada também encerra a ocorrência (TrocaController).
 * O efeito no status do equipamento fica no model (Ocorrencia::recolherEquipamento / colocarEquipamentoEmManutencao /
 * liberarEquipamento).
 */
class OcorrenciaController extends Controller
{
    public function index(Request $request)
    {
        $situacao = $request->input('situacao', 'abertas');
        $busca = trim((string) $request->input('busca', ''));

        $ocorrencias = Ocorrencia::query()
            ->with(['equipamento.tipoEquipamento', 'funcionario'])
            ->situacao($situacao)
            ->when($busca !== '', function ($consulta) use ($busca) {
                $like = "%{$busca}%";

                $consulta->where(fn ($q) => $q
                    ->when(ctype_digit($busca), fn ($q) => $q->orWhere('id', (int) $busca))
                    ->orWhere('problema', 'like', $like)
                    ->orWhere('protocolo', 'like', $like)
                    ->orWhereHas('equipamento', fn ($e) => $e
                        ->where('identificacao', 'like', $like)
                        ->orWhere('numero_serie', 'like', $like)
                        ->orWhere('patrimonio', 'like', $like)
                        ->orWhereRaw("CONCAT_WS(' ', fabricante, modelo) LIKE ?", [$like])));
            })
            ->orderByDesc('reportado_em')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('ocorrencias.index', compact('ocorrencias', 'situacao', 'busca'));
    }

    public function create(Request $request)
    {
        return view('ocorrencias.create', [
            'ocorrencia' => null,
            'equipamentoId' => $request->integer('equipamento_id') ?: null,
            ...$this->dadosFormulario(),
        ]);
    }

    public function store(StoreOcorrenciaRequest $request)
    {
        $ocorrencia = DB::transaction(function () use ($request) {
            $dados = $request->validated();
            $equipamento = Equipamento::findOrFail($dados['equipamento_id']);

            // Sem último usuário informado, vale quem está com o equipamento.
            $dados['funcionario_id'] ??= $equipamento->emprestimoEmAberto()?->movimentacao?->funcionario_id;

            $recolher = $dados['recolher'];
            unset($dados['recolher']);

            $ocorrencia = Ocorrencia::create([...$dados, 'usuario_id' => Auth::id()]);

            $recolher ? $ocorrencia->recolherEquipamento() : $ocorrencia->colocarEquipamentoEmManutencao();

            return $ocorrencia;
        });

        return redirect()
            ->route('ocorrencias.show', $ocorrencia)
            ->with('success', "Ocorrência #{$ocorrencia->id} registrada."
                . ($ocorrencia->devolucao_movimentacao_id ? " Equipamento recolhido: gere o termo de devolução #{$ocorrencia->devolucao_movimentacao_id} para assinatura." : ''));
    }

    public function show(Ocorrencia $ocorrencia)
    {
        $ocorrencia->load(['equipamento.tipoEquipamento', 'funcionario', 'usuario.funcionario', 'troca', 'devolucao']);

        return view('ocorrencias.show', [
            'ocorrencia' => $ocorrencia,
            'emprestimo' => $ocorrencia->equipamento?->emprestimoEmAberto(),
            'fornecedores' => Ocorrencia::query()->whereNotNull('fornecedor')->distinct()->orderBy('fornecedor')->pluck('fornecedor'),
        ]);
    }

    public function edit(Ocorrencia $ocorrencia)
    {
        $ocorrencia->load('equipamento.tipoEquipamento');

        return view('ocorrencias.edit', [
            'ocorrencia' => $ocorrencia,
            'equipamentoId' => $ocorrencia->equipamento_id,
            ...$this->dadosFormulario($ocorrencia),
        ]);
    }

    /** Corrige os dados; não abre nem encerra (ver encerrar() e reabrir()). */
    public function update(UpdateOcorrenciaRequest $request, Ocorrencia $ocorrencia)
    {
        $ocorrencia->update($request->validated());

        return redirect()
            ->route('ocorrencias.show', $ocorrencia)
            ->with('success', "Ocorrência #{$ocorrencia->id} atualizada.");
    }

    /** Liberação pela TI: registra a solução e os custos e devolve o equipamento a "Disponível" (se foi recolhido). */
    public function encerrar(EncerrarOcorrenciaRequest $request, Ocorrencia $ocorrencia)
    {
        DB::transaction(function () use ($request, $ocorrencia) {
            $ocorrencia->update($request->validated());
            $ocorrencia->liberarEquipamento();
        });

        return redirect()
            ->route('ocorrencias.show', $ocorrencia)
            ->with('success', "Ocorrência #{$ocorrencia->id} encerrada.");
    }

    /** Volta a ocorrência para "Aberta"; o equipamento com a TI retorna para "Em manutenção". */
    public function reabrir(Ocorrencia $ocorrencia)
    {
        if ($ocorrencia->estaAberta()) {
            return back()->with('error', "A ocorrência #{$ocorrencia->id} já está aberta.");
        }

        DB::transaction(function () use ($ocorrencia) {
            $ocorrencia->update(['liberado_em' => null]);
            $ocorrencia->colocarEquipamentoEmManutencao();
        });

        return redirect()
            ->route('ocorrencias.show', $ocorrencia)
            ->with('success', "Ocorrência #{$ocorrencia->id} reaberta.");
    }

    public function destroy(Ocorrencia $ocorrencia)
    {
        DB::transaction(function () use ($ocorrencia) {
            // Registro feito por engano: desfaz o "Em manutenção" que a própria ocorrência aplicou.
            if ($ocorrencia->estaAberta()) {
                $ocorrencia->liberarEquipamento();
            }

            $ocorrencia->delete();
        });

        return redirect()
            ->route('ocorrencias.index')
            ->with('success', "Ocorrência #{$ocorrencia->id} excluída.");
    }

    /** Listas do formulário: equipamentos do parque, funcionários, quem está com cada equipamento e sugestões. */
    private function dadosFormulario(?Ocorrencia $ocorrencia = null): array
    {
        $equipamentos = Equipamento::query()
            ->with('tipoEquipamento')
            ->whereNotIn('status', ['baixado', 'descartado'])
            ->when($ocorrencia, fn ($q) => $q->orWhere('id', $ocorrencia->equipamento_id))
            ->orderBy('id')
            ->get()
            ->mapWithKeys(fn (Equipamento $equipamento) => [$equipamento->id => collect([
                "#{$equipamento->id}",
                trim(($equipamento->tipoEquipamento?->nome ?? '') . ' ' . $equipamento->nome_exibicao),
                $equipamento->identificacao,
                $equipamento->numero_serie ? "S/N {$equipamento->numero_serie}" : null,
            ])->filter()->implode(' · ')]);

        $funcionarios = Funcionario::query()
            ->when($ocorrencia?->funcionario_id, fn ($q, $id) => $q->withTrashed()->where(fn ($q) => $q->whereNull('apagado_em')->orWhere('id', $id)))
            ->orderBy('nome')
            ->orderBy('sobrenome')
            ->get()
            ->mapWithKeys(fn (Funcionario $funcionario) => [
                $funcionario->id => $funcionario->nome_completo . ($funcionario->matricula ? " ({$funcionario->matricula})" : ''),
            ]);

        // equipamento_id => quem está com ele: preenche o último usuário e oferece recolher o equipamento.
        $responsaveis = MovimentacaoEquipamento::query()
            ->whereNull('devolvido_em')
            ->whereHas('movimentacao', fn ($m) => $m->comEmprestimo()->where('status', '!=', 'cancelada'))
            ->with('movimentacao.funcionario')
            ->get()
            ->mapWithKeys(fn (MovimentacaoEquipamento $item) => [$item->equipamento_id => [
                'funcionario_id' => $item->movimentacao->funcionario_id,
                'nome' => $item->movimentacao->funcionario?->nome_completo
                    . ($item->movimentacao->funcionario?->matricula ? " ({$item->movimentacao->funcionario->matricula})" : ''),
                'termo' => $item->movimentacao_id,
            ]]);

        $problemasAnteriores = Ocorrencia::query()
            ->select('problema')
            ->groupBy('problema')
            ->orderByRaw('MAX(reportado_em) DESC')
            ->limit(50)
            ->pluck('problema');

        $canais = collect(Ocorrencia::CANAIS_SUGERIDOS)
            ->merge(Ocorrencia::query()->whereNotNull('canal')->distinct()->pluck('canal'))
            ->unique(fn ($canal) => mb_strtolower($canal))
            ->values();

        return compact('equipamentos', 'funcionarios', 'responsaveis', 'problemasAnteriores', 'canais');
    }
}
