<?php

namespace App\Http\Controllers;

use App\Http\Requests\Usuarios\IndexRequest;
use App\Http\Requests\Usuarios\StoreUsuarioRequest;
use App\Http\Requests\Usuarios\UpdateUsuarioRequest;
use App\Models\Empresa;
use App\Models\Funcionario;
use App\Models\Setor;
use App\Models\Usuario;
use App\Support\Mask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        $dadosFiltro = $request->validated();

        $consultaUsuarios = Usuario::query()
            ->with(['funcionario']);


        if (!empty($dadosFiltro['id'])) {
            $consultaUsuarios->where('usuarios.id', $dadosFiltro['id']);
        }

        if (!empty($dadosFiltro['email'])) {
            $consultaUsuarios->where('usuarios.email', 'like', '%' . $dadosFiltro['email'] . '%');
        }

        if (array_key_exists('ativo', $dadosFiltro) && $dadosFiltro['ativo'] !== null) {
            $consultaUsuarios->where('usuarios.ativo', $dadosFiltro['ativo']);
        }

        if (!empty($dadosFiltro['funcionario'])) {
            $termoBuscaFuncionario = $dadosFiltro['funcionario'];

            $consultaUsuarios->whereHas('funcionario', function ($consultaFuncionario) use ($termoBuscaFuncionario) {
                $consultaFuncionario
                    ->where('funcionarios.nome', 'like', '%' . $termoBuscaFuncionario . '%')
                    ->orWhere('funcionarios.sobrenome', 'like', '%' . $termoBuscaFuncionario . '%')
                    ->orWhereRaw(
                        "CONCAT(funcionarios.nome, ' ', funcionarios.sobrenome) LIKE ?",
                        ['%' . $termoBuscaFuncionario . '%']
                    );
            });
        }

        if (!empty($dadosFiltro['busca'])) {
            $termoBusca = $dadosFiltro['busca'];
            $campoBusca = $dadosFiltro['campo'] ?? null;

            $consultaUsuarios->where(function ($consulta) use ($termoBusca, $campoBusca) {
                $termoBuscaNumerico = ctype_digit($termoBusca) ? (int) $termoBusca : null;

                if ($campoBusca === 'id') {
                    if ($termoBuscaNumerico !== null) {
                        $consulta->where('usuarios.id', $termoBuscaNumerico);
                    } else {
                        $consulta->whereRaw('1 = 0');
                    }
                    return;
                }

                if ($campoBusca === 'email') {
                    $consulta->where('usuarios.email', 'like', '%' . $termoBusca . '%');
                    return;
                }

                if ($campoBusca === 'funcionario') {
                    $consulta->whereHas('funcionario', function ($consultaFuncionario) use ($termoBusca) {
                        $consultaFuncionario
                            ->where('funcionarios.nome', 'like', '%' . $termoBusca . '%')
                            ->orWhere('funcionarios.sobrenome', 'like', '%' . $termoBusca . '%')
                            ->orWhereRaw(
                                "CONCAT(funcionarios.nome, ' ', funcionarios.sobrenome) LIKE ?",
                                ['%' . $termoBusca . '%']
                            );
                    });

                    return;
                }

                $consulta->where(function ($subconsulta) use ($termoBusca, $termoBuscaNumerico) {
                    if ($termoBuscaNumerico !== null) {
                        $subconsulta->orWhere('usuarios.id', $termoBuscaNumerico);
                    }

                    $subconsulta->orWhere('usuarios.email', 'like', '%' . $termoBusca . '%');

                    $subconsulta->orWhereHas('funcionario', function ($consultaFuncionario) use ($termoBusca) {
                        $consultaFuncionario
                            ->where('funcionarios.nome', 'like', '%' . $termoBusca . '%')
                            ->orWhere('funcionarios.sobrenome', 'like', '%' . $termoBusca . '%')
                            ->orWhereRaw(
                                "CONCAT(funcionarios.nome, ' ', funcionarios.sobrenome) LIKE ?",
                                ['%' . $termoBusca . '%']
                            );
                    });
                });
            });
        }

        $colunaOrdenacao = $dadosFiltro['ordenar_por'] ?? 'id';
        $direcaoOrdenacao = $dadosFiltro['direcao'] ?? 'asc';

        if ($colunaOrdenacao === 'funcionario') {
            $consultaUsuarios
                ->leftJoin('funcionarios', 'funcionarios.id', '=', 'usuarios.funcionario_id')
                ->select('usuarios.*')
                ->orderByRaw(
                    "funcionarios.nome {$direcaoOrdenacao}, funcionarios.sobrenome {$direcaoOrdenacao}"
                );
        } else {
            $colunasPermitidas = ['id', 'email', 'ultimo_login', 'ativo'];

            if (!in_array($colunaOrdenacao, $colunasPermitidas, true)) {
                $colunaOrdenacao = 'id';
            }

            $direcaoOrdenacao = $direcaoOrdenacao === 'desc' ? 'desc' : 'asc';

            $consultaUsuarios->orderBy('usuarios.' . $colunaOrdenacao, $direcaoOrdenacao);
        }

        $listaDeUsuarios = $consultaUsuarios
            ->paginate(25)
            ->withQueryString();

        return view('usuarios.index', [
            'listaDeUsuarios'   => $listaDeUsuarios,
            'termoBusca'        => $dadosFiltro['busca'] ?? null,
            'colunaOrdenacao'   => $colunaOrdenacao,
            'direcaoOrdenacao'  => $direcaoOrdenacao,
        ]);
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

    public function show(Usuario $usuario)
    {
        $usuario->load('funcionario');

        return view('usuarios.show', [
            'usuario' => $usuario,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Usuario $usuario)
    {
        $usuario->load(['funcionario.setor.empresa']);

        return view('usuarios.edit', [
            'usuario' => $usuario,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUsuarioRequest $request, Usuario $usuario)
    {
        $dados = $request->validated();

        $usuario->email = $dados['email'];

        $usuario->ativo = $dados['ativo'] ?? false;

        if (!empty($dados['senha'])) {
            $usuario->senha = Hash::make($dados['senha']);
        }

        $usuario->save();

        return redirect()
            ->route('usuarios.index', $usuario->id)
            ->with('success', 'Usuário atualizado com sucesso.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Usuario $usuario)
    {
        if ($usuario->trashed()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'O funcionário já está excluído.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuário  removido com sucesso.');
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
