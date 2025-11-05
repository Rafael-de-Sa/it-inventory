<?php

namespace App\Http\Controllers;

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
    public function index()
    {
        //
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

    /**
     * Armazena a nova movimentação e vincula os equipamentos.
     * Store a newly created resource in storage.
     */
    public function store(StoreMovimentacaoRequest $request)
    {
        $dadosValidados = $request->validated();

        DB::transaction(function () use ($dadosValidados) {
            /** @var Movimentacao $movimentacao */
            $movimentacao = Movimentacao::create([
                'setor_id'       => $dadosValidados['setor_id'],
                'funcionario_id' => $dadosValidados['funcionario_id'],
                'observacao'     => $dadosValidados['observacao'] ?? null,
                // demais campos padrão do model (data_movimentacao, status, etc.)
            ]);

            $idsEquipamentos = $dadosValidados['equipamentos'];

            // trava os equipamentos durante a operação
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
