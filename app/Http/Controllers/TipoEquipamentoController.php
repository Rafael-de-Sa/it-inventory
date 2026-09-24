<?php

namespace App\Http\Controllers;

use App\Http\Requests\TipoEquipamentos\IndexRequest;
use App\Http\Requests\TipoEquipamentos\StoreTipoEquipamentoRequest;
use App\Http\Requests\TipoEquipamentos\UpdateTipoEquipamentoRequest;
use App\Models\TipoEquipamento;

class TipoEquipamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        $dadosValidados = $request->validated();

        $campo      = $dadosValidados['campo']       ?? 'id';
        $busca      = $dadosValidados['busca']       ?? null;
        $ativo      = $dadosValidados['ativo']       ?? null;
        $ordenarPor = $dadosValidados['ordenar_por'] ?? 'id';
        $direcao    = $dadosValidados['direcao']     ?? 'asc';

        $tipos = TipoEquipamento::query()
            ->when(in_array($ativo, ['0', '1'], true), fn ($query) => $query->where('ativo', $ativo === '1'))
            ->when(filled($busca), fn ($query) => $campo === 'id'
                ? $query->where('id', $busca)
                : $query->where('nome', 'like', "%{$busca}%"))
            ->orderBy($ordenarPor, $direcao)
            ->paginate(25)
            ->withQueryString();

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
        $tipoEquipamento->loadCount([
            'equipamentos',
            'equipamentos as equipamentos_ativos_count' => function ($query) {
                $query->where('ativo', 1);
            },
        ]);

        if ($tipoEquipamento->equipamentos_count > 0) {
            $mensagem = "Não é possível excluir: há {$tipoEquipamento->equipamentos_count} equipamento(s) vinculado(s) a este tipo de equipamento";

            if ($tipoEquipamento->equipamentos_ativos_count > 0) {
                $mensagem .= " ({$tipoEquipamento->equipamentos_ativos_count} ativo(s)).";
            } else {
                $mensagem .= ".";
            }

            return back()->with('error', $mensagem);
        }

        try {
            $tipoEquipamento->delete();

            return to_route('tipo-equipamentos.index')
                ->with('success', 'Tipo de equipamento excluído com sucesso!');
        } catch (\Throwable $erro) {
            report($erro);

            return back()->with('error', 'Erro ao excluir o tipo de equipamento. Tente novamente.');
        }
    }
}
