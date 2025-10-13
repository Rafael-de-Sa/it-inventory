<?php

namespace App\Http\Controllers;

use App\Http\Requests\Empresas\IndexRequest;
use App\Http\Requests\Empresas\StoreEmpresasRequest;
use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        $dadosValidados = $request->validated();


        $termoBusca = $dadosValidados['busca'] ?? null;
        $colunaOrdenacao = $dadosValidados['ordenar_por'] ?? 'id';
        $direcaoOrdenacao = $dadosValidados['direcao'] ?? 'asc';
        $paginacao = 10;
        $campo = $dadosValidados['campo'] ?? null;
        $ativo = $dadosValidados['ativo'] ?? null;

        $listaDeEmpresas = Empresa::query()
            ->when($termoBusca, function ($consulta) use ($termoBusca, $campo) {
                $consulta->where(function ($grupo) use ($termoBusca, $campo) {
                    // se escolher um campo, aplica só nele; senão, busca global
                    if ($campo) {
                        $grupo->where($campo, 'like', "%{$termoBusca}%");
                        return;
                    }
                    $grupo->where('razao_social', 'like', "%{$termoBusca}%")
                        ->orWhere('nome_fantasia', 'like', "%{$termoBusca}%")
                        // ->orWhere('email', 'like', "%{$termoBusca}%")
                        ->orWhere('cidade', 'like', "%{$termoBusca}%")
                        ->orWhere('estado', 'like', "%{$termoBusca}%")
                        ->orWhere('id', 'like', "%{$termoBusca}%")
                        ->orWhere('cnpj', 'like', "%{$termoBusca}%");
                });
            })
            ->when(
                $ativo !== null && $ativo !== '',
                fn($q) => $q->where('ativo', (int)$ativo)
            )
            ->orderBy($colunaOrdenacao, $direcaoOrdenacao)
            ->paginate($paginacao)
            ->withQueryString();

        return view('empresas.index', [
            'listaDeEmpresas' => $listaDeEmpresas,
            'termoBusca' => $termoBusca,
            'colunaOrdenacao' => $colunaOrdenacao,
            'direcaoOrdenacao' => $direcaoOrdenacao,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return  view('empresas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmpresasRequest $request)
    {

        $data = $request->validated();
        $empresa = Empresa::create($data);

        return to_route('empresas.create')
            ->with('success', 'Empresa criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $empresa = Empresa::findOrFail($id);
        return view('empresas.show', compact('empresa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $empresa = Empresa::findOrFail($id);
        return view('empresas.edit', compact('empresa'));
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
        $empresa = Empresa::findOrFail($id);
        dd($empresa);
    }
}
