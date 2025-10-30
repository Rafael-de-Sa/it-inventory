<?php

namespace App\Http\Controllers;

use App\Http\Requests\TipoEquipamentos\IndexRequest;
use App\Http\Requests\TipoEquipamentos\StoreTipoEquipamentoRequest;
use App\Http\Requests\TipoEquipamentos\UpdateTipoEquipamentoRequest;
use App\Models\TipoEquipamento;
use Illuminate\Http\Request;

class TipoEquipamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TipoEquipamento::query();

        // Status
        $status = $request->string('status', 'ativos');
        if ($status === 'ativos') $query->where('ativo', true);
        elseif ($status === 'inativos') $query->where('ativo', false);

        // Busca
        $campo = $request->string('campo', 'id');
        $busca = $request->string('busca');
        if ($busca->isNotEmpty()) {
            if ($campo === 'id') $query->where('id', $busca);
            if ($campo === 'nome') $query->where('nome', 'like', "%{$busca}%");
        }

        // Ordenação
        $ordenarPor = $request->string('ordenar_por', 'id');
        $direcao = $request->string('direcao', 'asc');
        $query->orderBy(in_array($ordenarPor, ['id', 'nome']) ? $ordenarPor : 'id', $direcao === 'desc' ? 'desc' : 'asc');

        $tipos = $query->paginate(25);

        return view('tipo-equipamentos.index', compact('tipos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tipo-equipamentos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTipoEquipamentoRequest $request)
    {
        $dados = $request->validated();
        TipoEquipamento::create($dados);

        return redirect()
            ->route('tipo-equipamentos.index')
            ->with('success', "Tipo de equipamento cadastrado com sucesso.");
    }

    /**
     * Display the specified resource.
     */
    public function show(TipoEquipamento $tipoEquipamento)
    {
        return view('tipo-equipamentos.show', compact('tipoEquipamento'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TipoEquipamento $tipoEquipamento)
    {
        return view('tipo-equipamentos.edit', compact('tipoEquipamento'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTipoEquipamentoRequest $request, TipoEquipamento $tipoEquipamento)
    {
        $data = $request->validated();

        $tipoEquipamento->update($data);

        return to_route('tipo-equipamentos.index')
            ->with('success', 'Tipo de equipamento atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipoEquipamento $tipoEquipamento)
    {

        $tipoEquipamento->loadCount('equipamentos');

        if ($tipoEquipamento->equipamentos_count > 0) {
            return back()->with(
                'error',
                "Não é possível excluir: há {$tipoEquipamento->equipamentos_count} equipamento(s) vinculado(s) a este tipo."
            );
        }

        try {
            $tipoEquipamento->delete();

            return to_route('tipo-equipamentos.index')
                ->with('success', 'Tipo de equipamento excluído com sucesso!');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Erro ao excluir o tipo de equipamento. Tente novamente.');
        }
    }
}
