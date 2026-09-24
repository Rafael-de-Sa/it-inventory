<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ocorrencias\OcorrenciaRequest;
use App\Http\Requests\Ocorrencias\StoreOcorrenciaRequest;
use App\Models\Equipamento;
use App\Models\Funcionario;
use App\Models\MovimentacaoEquipamento;
use App\Models\Ocorrencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Ocorrências de equipamento (issue #10): problema reportado, acompanhamento e liberação pela TI.
 * O efeito no status do equipamento fica no model (Ocorrencia::colocarEquipamentoEmManutencao / liberarEquipamento).
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

            $ocorrencia = Ocorrencia::create([...$dados, 'usuario_id' => Auth::id()]);
            $ocorrencia->colocarEquipamentoEmManutencao();

            return $ocorrencia;
        });

        return redirect()
            ->route('ocorrencias.show', $ocorrencia)
            ->with('success', "Ocorrência #{$ocorrencia->id} registrada.");
    }

    public function show(Ocorrencia $ocorrencia)
    {
        $ocorrencia->load(['equipamento.tipoEquipamento', 'funcionario', 'usuario.funcionario', 'troca']);

        return view('ocorrencias.show', [
            'ocorrencia' => $ocorrencia,
            'emprestimo' => $ocorrencia->equipamento?->emprestimoEmAberto(),
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

    public function update(OcorrenciaRequest $request, Ocorrencia $ocorrencia)
    {
        DB::transaction(function () use ($request, $ocorrencia) {
            $estavaAberta = $ocorrencia->estaAberta();

            $ocorrencia->update($request->validated());

            if ($estavaAberta && ! $ocorrencia->estaAberta()) {
                $ocorrencia->liberarEquipamento();
            } elseif (! $estavaAberta && $ocorrencia->estaAberta()) {
                // Reaberta: volta para manutenção se o equipamento estiver com a TI.
                $ocorrencia->colocarEquipamentoEmManutencao();
            }
        });

        return redirect()
            ->route('ocorrencias.show', $ocorrencia)
            ->with('success', "Ocorrência #{$ocorrencia->id} atualizada.");
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

        // equipamento_id => funcionario_id de quem está com ele (para sugerir o último usuário no formulário).
        $responsaveis = MovimentacaoEquipamento::query()
            ->whereNull('devolvido_em')
            ->whereHas('movimentacao', fn ($m) => $m->comEmprestimo()->where('status', '!=', 'cancelada'))
            ->with('movimentacao:id,funcionario_id')
            ->get()
            ->mapWithKeys(fn (MovimentacaoEquipamento $item) => [$item->equipamento_id => $item->movimentacao->funcionario_id]);

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
