<?php

namespace App\Http\Controllers;

use App\Http\Requests\Funcionarios\IndexRequest;
use App\Http\Requests\Funcionarios\StoreFuncionarioRequest;
use App\Http\Requests\Funcionarios\UpdateFuncionarioRequest;
use App\Models\Empresa;
use App\Models\Funcionario;
use App\Models\Movimentacao;
use App\Models\MovimentacaoEquipamento;
use App\Models\Setor;
use App\Support\Mask;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FuncionarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request): \Illuminate\View\View
    {
        $validados   = $request->validated();

        $opcoesCampo = [
            'id' => 'ID',
            'nome' => 'Nome',
            'empresa_nome' => 'Nome Empresa',
            'empresa_cnpj' => 'CNPJ Empresa',
            'setor_nome' => 'Nome Setor',
            'matricula' => 'Matrícula',
        ];

        $opcoesOrdenacao = [
            'id' => 'ID',
            'nome' => 'Nome',
            'empresa_nome' => 'Nome Empresa',
            'empresa_cnpj' => 'CNPJ Empresa',
            'setor_nome' => 'Nome Setor',
            'matricula' => 'Matrícula',
        ];

        $campo = $validados['campo']        ?? 'id';
        $busca = $validados['busca']        ?? null;
        $ordenarPor = $validados['ordenar_por']  ?? 'id';
        $direcao = $validados['direcao']      ?? 'asc';
        $ativo = $validados['ativo']        ?? 'todos';
        $terceirizado = $validados['terceirizado'] ?? 'todos';

        $mapaOrdenacao = [
            'id' => 'funcionarios.id',
            'nome' => 'funcionarios.nome',
            'matricula' => 'funcionarios.matricula',
            'empresa_nome' => 'empresas.nome_fantasia',
            'empresa_cnpj' => 'empresas.cnpj',
            'setor_nome' => 'setores.nome',
        ];

        $consulta = Funcionario::query()
            ->with('usuario') // para saber se pertence ao usuário logado
            ->leftJoin('setores', 'setores.id', '=', 'funcionarios.setor_id')
            ->leftJoin('empresas', 'empresas.id', '=', 'setores.empresa_id')
            ->select([
                'funcionarios.*',
                'empresas.nome_fantasia as empresa_nome',
                'empresas.cnpj as empresa_cnpj',
                'setores.nome as setor_nome',
            ]);

        if ($ativo !== 'todos') {
            $consulta->where('funcionarios.ativo', (int) $ativo);
        }
        if ($terceirizado !== 'todos') {
            $consulta->where('funcionarios.terceirizado', (int) $terceirizado);
        }

        if ($busca !== null && $busca !== '') {
            if ($campo === 'nome') {
                $consulta->where(function ($q) use ($busca) {
                    $q->where('funcionarios.nome', 'like', "%{$busca}%")
                        ->orWhere('funcionarios.sobrenome', 'like', "%{$busca}%");
                });
            } elseif ($campo === 'empresa_cnpj') {
                $buscaNumeros = preg_replace('/\D+/', '', $busca) ?? '';
                $exprCnpjSomenteDigitos = "REPLACE(REPLACE(REPLACE(REPLACE(empresas.cnpj, '.', ''), '-', ''), '/', ''), ' ', '')";
                $consulta->whereRaw("$exprCnpjSomenteDigitos LIKE ?", ["%{$buscaNumeros}%"]);
            } else {
                $coluna = Arr::get($mapaOrdenacao, $campo, 'funcionarios.id');
                $consulta->where($coluna, 'like', '%' . $busca . '%');
            }
        }

        if ($ordenarPor === 'nome') {
            $consulta->orderBy('funcionarios.nome', $direcao)
                ->orderBy('funcionarios.sobrenome', $direcao);
        } else {
            $colunaOrdenacao = Arr::get($mapaOrdenacao, $ordenarPor, 'funcionarios.id');
            $consulta->orderBy($colunaOrdenacao, $direcao);
        }

        $funcionarios = $consulta->paginate(10)->appends($validados);

        return view('funcionarios.index', [
            'funcionarios' => $funcionarios,
            'opcoesCampo' => $opcoesCampo,
            'opcoesOrdenacao' => $opcoesOrdenacao,
            'usuarioLogado' => Auth::user(),
        ]);
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

        $dados['cpf'] = Mask::digits($dados['cpf'] ?? '', 11);
        if (!empty($dados['telefone'])) {
            $dados['telefone'] = Mask::digits($dados['telefone'], 11);
        }

        $empresaId = $dados['empresa_id'] ?? null;
        $setorId = $dados['setor_id'] ?? null;

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

        unset($dados['empresa_id']);

        $funcionario = DB::transaction(function () use ($dados) {
            return Funcionario::create($dados);
        });

        return redirect()
            ->route('funcionarios.index')
            ->with('success', 'Funcionário criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */

    public function show(Funcionario $funcionario)
    {
        $funcionario->load([
            'setor:id,nome,empresa_id',
            'setor.empresa:id,nome_fantasia,cnpj',
            'usuario:id,funcionario_id,ativo',
        ]);

        $restricoesDesligamento   = $funcionario->obterRestricoesDesligamento();
        $podeRealizarDesligamento = $funcionario->podeSerDesligado();

        $usuarioLogado = Auth::user();

        $funcionarioPertenceAoUsuarioLogado = false;

        if ($usuarioLogado && $funcionario->usuario) {
            $funcionarioPertenceAoUsuarioLogado = $usuarioLogado->id === $funcionario->usuario->id;
        }
        $podeMostrarBotoesGerenciais = ! $funcionarioPertenceAoUsuarioLogado;
        $podeMostrarBotaoDesligar = $podeMostrarBotoesGerenciais && $podeRealizarDesligamento;
        $podeMostrarBotaoExcluir = $podeMostrarBotoesGerenciais && $podeRealizarDesligamento;

        return view('funcionarios.show', [
            'funcionario' => $funcionario,
            'restricoesDesligamento' => $restricoesDesligamento,
            'podeRealizarDesligamento' => $podeRealizarDesligamento,
            'funcionarioPertenceAoUsuarioLogado' => $funcionarioPertenceAoUsuarioLogado,
            'podeMostrarBotaoDesligar' => $podeMostrarBotaoDesligar,
            'podeMostrarBotaoExcluir' => $podeMostrarBotaoExcluir,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Funcionario $funcionario)
    {

        $funcionario->load(['setor:id,nome,empresa_id', 'setor.empresa:id,nome_fantasia']);

        $opcoesEmpresas = Empresa::query()
            ->orderBy('nome_fantasia')
            ->pluck('nome_fantasia', 'id');

        $empresaSelecionadaId = optional($funcionario->setor)->empresa_id;

        $opcoesSetores = collect();
        if ($empresaSelecionadaId) {
            $opcoesSetores = Setor::query()
                ->where('empresa_id', $empresaSelecionadaId)
                ->orderBy('nome')
                ->pluck('nome', 'id');
        }

        return view('funcionarios.edit', compact(
            'funcionario',
            'opcoesEmpresas',
            'opcoesSetores',
            'empresaSelecionadaId'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFuncionarioRequest $request, Funcionario $funcionario)
    {

        $dados = $request->validated();

        $dados['terceirizado'] = $request->boolean('terceirizado');
        $dados['ativo']        = $request->boolean('ativo');

        if (array_key_exists('ativo', $dados)) {
            if ($dados['ativo'] === false && $funcionario->ativo === true) {
                $dados['desligado_em'] = now();
            } elseif ($dados['ativo'] === true) {
                $dados['desligado_em'] = null;
            }
        }

        unset($dados['empresa_id']);

        $funcionario->update($dados);

        return redirect()
            ->route('funcionarios.index', $funcionario->id)
            ->with('success', 'Funcionário atualizado com sucesso.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Funcionario $funcionario)
    {
        $funcionario->loadMissing('usuario');

        if ($funcionario->usuario) {
            $usuario = $funcionario->usuario;

            $usuario->ativo = false;
            $usuario->save();
        }

        $funcionario->delete();

        return redirect()->route('funcionarios.index')
            ->with('success', 'Funcionário removido com sucesso.');
    }

    public function desligar(Funcionario $funcionario)
    {
        $restricoes = $funcionario->obterRestricoesDesligamento();

        if ($restricoes['ja_desligado']) {
            return redirect()
                ->route('funcionarios.show', $funcionario->id)
                ->with('error', 'Este funcionário já está marcado como desligado.');
        }

        if ($restricoes['equipamentos_em_uso']) {
            return redirect()
                ->route('funcionarios.show', $funcionario->id)
                ->with('error', 'Não é possível realizar o desligamento: ainda existem equipamentos sob responsabilidade do funcionário.');
        }

        if ($restricoes['termos_responsabilidade_pendentes'] || $restricoes['termos_devolucao_pendentes']) {
            return redirect()
                ->route('funcionarios.show', $funcionario->id)
                ->with('error', 'Não é possível realizar o desligamento: existem termos de responsabilidade ou devolução pendentes de upload.');
        }

        DB::transaction(function () use ($funcionario) {
            $funcionario->desligado_em = today();
            $funcionario->ativo = false;
            $funcionario->save();

            $funcionario->loadMissing('usuario');

            if ($funcionario->usuario) {
                $usuario = $funcionario->usuario;
                $usuario->delete();
            }
        });

        return redirect()
            ->route('funcionarios.show', $funcionario->id)
            ->with('success', 'Desligamento do funcionário e inativação do usuário vinculados realizados com sucesso.');
    }
}
