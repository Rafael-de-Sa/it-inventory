<?php

namespace App\Http\Controllers;

use App\Http\Requests\Usuarios\StoreUsuarioRequest;
use App\Models\Empresa;
use App\Models\Funcionario;
use App\Models\Setor;
use App\Models\Usuario;
use App\Support\Mask;
use Illuminate\Http\Request;

class UsuarioController extends Controller
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
        $listaEmpresas = Empresa::aptasParaUsuario()
            ->select('id', 'razao_social', 'cnpj')
            ->orderBy('razao_social')
            ->get();

        $opcoesEmpresas = $listaEmpresas->mapWithKeys(function ($empresa) {
            $rotulo = "{$empresa->razao_social} — " . Mask::cnpj($empresa->cnpj);
            return [$empresa->id => $rotulo];
        });

        return view('usuarios.create', [
            'opcoesEmpresas' => $opcoesEmpresas,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUsuarioRequest $request)
    {
        $dados = $request->validated();

        $usuario = Usuario::create($dados);

        return redirect()
            ->route('usuarios.show', $usuario)
            ->with('sucesso', 'Usuário cadastrado com sucesso.');
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

    public function setoresAtivos(Empresa $empresa)
    {
        $setores = $empresa->setores()
            ->where('ativo', true)
            ->whereNull('apagado_em')
            ->whereHas('funcionarios', fn($q) => $q->aptosParaUsuario())
            ->orderBy('nome')
            ->get(['id', 'nome']);

        return response()->json($setores);
    }

    public function funcionariosPorSetor(Setor $setor)
    {
        $funcionarios = $setor->funcionarios()
            ->where('ativo', true)
            ->whereNull('desligado_em')
            ->where('terceirizado', false)
            ->whereDoesntHave('usuario')
            ->whereNull('apagado_em')
            ->orderBy('nome')
            ->get(['id', 'nome', 'sobrenome', 'matricula']);

        return response()->json(
            $funcionarios->map(fn($f) => [
                'id' => $f->id,
                'rotulo' => trim(($f->nome ?? '') . ' ' . ($f->sobrenome ?? '')) . ($f->matricula ? " ({$f->matricula})" : ''),
            ])
        );
    }
}
