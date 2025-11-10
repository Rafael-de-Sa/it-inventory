<?php

namespace App\Http\Controllers;

use App\Http\Requests\Movimentacoes\IndexRequest;
use App\Http\Requests\Movimentacoes\StoreMovimentacaoRequest;
use App\Models\Empresa;
use App\Models\Equipamento;
use App\Models\Funcionario;
use App\Models\Movimentacao;
use App\Models\Setor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovimentacaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        $dados = $request->validated();

        $query = Movimentacao::query()
            ->with([
                'setor.empresa',
                'funcionario',
                'equipamentos',
            ])
            ->whereNull('apagado_em');

        if (!empty($dados['busca'])) {
            $busca = trim($dados['busca']);

            if (ctype_digit($busca)) {
                $query->where('id', (int) $busca);
            } else {
                // Se o usuário digitar algo não numérico, não aplica filtro por ID
                // (poderia forçar "sem resultados", mas preferi ignorar)
            }
        }

        if (!empty($dados['empresa_id'])) {
            $empresaId = (int) $dados['empresa_id'];

            $query->whereHas('setor', function ($subQuery) use ($empresaId) {
                $subQuery->where('empresa_id', $empresaId)
                    ->whereNull('apagado_em');
            });
        }

        // Setor (só se empresa foi informada, conforme sua regra)
        if (!empty($dados['setor_id']) && !empty($dados['empresa_id'])) {
            $query->where('setor_id', (int) $dados['setor_id']);
        }

        // Funcionário (só se empresa e setor foram informados)
        if (!empty($dados['funcionario_id']) && !empty($dados['empresa_id']) && !empty($dados['setor_id'])) {
            $query->where('funcionario_id', (int) $dados['funcionario_id']);
        }

        // Status
        if (!empty($dados['status'])) {
            $query->where('status', $dados['status']);
        }

        // -----------------------------
        // Ordenação
        // -----------------------------
        $colunaOrdenacao  = $dados['ordenar_por'] ?? 'id';
        $direcaoOrdenacao = $dados['direcao'] ?? 'asc';

        switch ($colunaOrdenacao) {
            case 'id':
                $colunaBanco = 'id';
                break;

            case 'status':
                $colunaBanco = 'status';
                break;

            case 'data':
            default:
                $colunaBanco  = 'criado_em';
                $colunaOrdenacao = 'data';
                break;
        }

        $query->orderBy($colunaBanco, $direcaoOrdenacao);

        $listaDeMovimentacoes = $query
            ->paginate(25)
            ->withQueryString();

        // -----------------------------
        // Dados para os filtros (combos)
        // -----------------------------

        // Empresas (pode ser aptasParaMovimentacao ou outro scope que você quiser usar)
        $listaDeEmpresas = Empresa::query()
            ->aptasParaMovimentacao()
            ->orderBy('razao_social')
            ->orderBy('nome_fantasia')
            ->get();

        $listaDeSetores = collect();
        if (!empty($dados['empresa_id'])) {
            $listaDeSetores = Setor::query()
                ->where('empresa_id', (int) $dados['empresa_id'])
                ->whereNull('apagado_em')
                ->orderBy('nome')
                ->get();
        }

        $listaDeFuncionarios = collect();
        if (!empty($dados['setor_id'])) {
            $listaDeFuncionarios = Funcionario::query()
                ->where('setor_id', (int) $dados['setor_id'])
                ->whereNull('apagado_em')
                ->orderBy('nome')
                ->orderBy('sobrenome')
                ->get();
        }

        $termoBusca = $dados['busca'] ?? null;

        return view('movimentacoes.index', [
            'listaDeMovimentacoes' => $listaDeMovimentacoes,
            'listaDeEmpresas'      => $listaDeEmpresas,
            'listaDeSetores'       => $listaDeSetores,
            'listaDeFuncionarios'  => $listaDeFuncionarios,
            'termoBusca'           => $termoBusca,
            'colunaOrdenacao'      => $colunaOrdenacao,
            'direcaoOrdenacao'     => $direcaoOrdenacao,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $listaDeEmpresas = Empresa::query()
            ->aptasParaMovimentacao()
            ->orderBy('razao_social')
            ->orderBy('nome_fantasia')
            ->get();

        $listaDeEquipamentos = Equipamento::query()
            ->where('ativo', true)
            ->whereNull('apagado_em')
            ->where('status', 'disponivel')
            ->orderBy('patrimonio')
            ->orderBy('id')
            ->get();

        return view('movimentacoes.create', compact('listaDeEmpresas', 'listaDeEquipamentos'));
    }

    public function store(StoreMovimentacaoRequest $request)
    {
        $dadosValidados = $request->validated();

        DB::transaction(function () use ($dadosValidados) {
            $movimentacao = Movimentacao::create([
                'setor_id'       => $dadosValidados['setor_id'],
                'funcionario_id' => $dadosValidados['funcionario_id'],
                'observacao'     => $dadosValidados['observacao'] ?? null,
            ]);

            $idsEquipamentos = $dadosValidados['equipamentos'];

            $equipamentos = Equipamento::query()
                ->whereIn('id', $idsEquipamentos)
                ->lockForUpdate()
                ->get();

            foreach ($equipamentos as $equipamento) {
                // vincula na tabela pivot movimentacao_equipamentos
                $movimentacao->equipamentos()->attach($equipamento->id);

                // marca equipamento como "em_uso"
                $equipamento->update([
                    'status' => 'em_uso',
                ]);
            }
        });

        return redirect()
            ->route('movimentacoes.index')
            ->with('success', 'Movimentação gerada com sucesso.');
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function setoresParaMovimentacao(Empresa $empresa)
    {
        $setores = $empresa->setores()
            ->where('ativo', true)
            ->whereNull('apagado_em')
            ->whereHas('funcionarios', function ($query) {
                $query->where('ativo', true)
                    ->whereNull('desligado_em')
                    ->whereNull('apagado_em');
            })
            ->orderBy('nome')
            ->get(['id', 'nome']);

        return response()->json($setores);
    }

    public function funcionariosParaMovimentacao(Setor $setor)
    {
        $funcionarios = $setor->funcionarios()
            ->where('ativo', true)
            ->whereNull('desligado_em')
            ->whereNull('apagado_em')
            ->orderBy('nome')
            ->orderBy('sobrenome')
            ->get(['id', 'nome', 'sobrenome', 'matricula', 'terceirizado']);

        $payload = $funcionarios->map(function (Funcionario $funcionario) {
            $nomeCompleto = trim(($funcionario->nome ?? '') . ' ' . ($funcionario->sobrenome ?? ''));

            $rotulo = $nomeCompleto;
            if ($funcionario->matricula) {
                $rotulo .= " ({$funcionario->matricula})";
            }
            if ($funcionario->terceirizado) {
                $rotulo .= ' - Terceirizado';
            }

            return [
                'id'     => $funcionario->id,
                'rotulo' => $rotulo,
            ];
        });

        return response()->json($payload);
    }
}
