<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmpresaRequest;
use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
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
        return  view('empresas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmpresaRequest $request)
    {

        $empresa = new Empresa();

        $empresa->nome_fantasia = $request->nome_fantasia;
        $empresa->razao_social = $request->razao_social;
        $empresa->cnpj = $request->cnpj;
        $empresa->rua = $request->rua;
        $empresa->numero = $request->numero;
        $empresa->complemento = $request->complemento;
        $empresa->bairro = $request->bairro;
        $empresa->cidade = $request->cidade;
        $empresa->estado = $request->estado;
        $empresa->cep = $request->cep;
        $empresa->email = $request->email;
        $empresa->telefones = $request->telefones;


        $empresa->save();
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
