<?php

namespace App\Http\Controllers;

use App\Http\Requests\Funcionarios\StoreFuncionarioRequest;
use App\Models\Empresa;
use App\Models\Funcionario;
use App\Models\Setor;
use App\Support\Mask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FuncionarioController extends Controller
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
        $listaEmpresas = Empresa::select('id', 'razao_social', 'cnpj')
            ->whereNull('apagado_em')
            ->where('ativo', true)
            ->orderBy('id')
            ->get();

        $opcoesEmpresas = $listaEmpresas->mapWithKeys(function ($empresa) {
            $rotulo = "{$empresa->razao_social} — " . Mask::cnpj($empresa->cnpj);
            return [$empresa->id => $rotulo];
        });

        return view('funcionarios.create', [
            'opcoesEmpresas' => $opcoesEmpresas,
        ]);
    }

    /**
     * Retorna os setores vinculados a uma empresa (usado por AJAX)
     */
    public function setoresPorEmpresa(Empresa $empresa)
    {
        $setores = $empresa->setores()
            ->select('id', 'nome')
            ->whereNull('apagado_em')
            ->where('ativo', true)
            ->orderBy('id')
            ->get();

        return response()->json($setores);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFuncionarioRequest $request)
    {
        $dados = $request->validated();

        // 1) Normalizações (remove máscara)
        $dados['cpf'] = Mask::digits($dados['cpf'] ?? '', 11);
        if (!empty($dados['telefone'])) {
            $dados['telefone'] = Mask::digits($dados['telefone'], 11);
        }

        // 2) Garantir vínculo: setor pertence à empresa escolhida
        $empresaId = $dados['empresa_id'] ?? null;
        $setorId   = $dados['setor_id'] ?? null;

        if ($empresaId && $setorId) {
            $pertence = Setor::where('id', $setorId)
                ->where('empresa_id', $empresaId)
                ->exists();

            if (!$pertence) {
                return back()
                    ->withErrors(['setor_id' => 'O setor selecionado não pertence à empresa escolhida.'])
                    ->withInput();
            }
        }

        // 3) Remover empresa_id antes de persistir (tabela funcionários tem setor_id)
        unset($dados['empresa_id']);

        // 4) Persistência
        $funcionario = DB::transaction(function () use ($dados) {
            // 'ativo' vem como default true na migration/model
            return Funcionario::create($dados);
        });

        // 5) Redirect
        return redirect()
            ->route('funcionarios.show', $funcionario->id)
            ->with('sucesso', 'Funcionário criado com sucesso.');
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
}
