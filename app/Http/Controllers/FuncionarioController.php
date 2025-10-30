<?php

namespace App\Http\Controllers;

use App\Http\Requests\Funcionarios\IndexRequest;
use App\Http\Requests\Funcionarios\StoreFuncionarioRequest;
use App\Http\Requests\Funcionarios\UpdateFuncionarioRequest;
use App\Models\Empresa;
use App\Models\Funcionario;
use App\Models\Setor;
use App\Support\Mask;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
            'id'            => 'ID',
            'nome'          => 'Nome',
            'empresa_nome'  => 'Nome Empresa',
            'empresa_cnpj'  => 'CNPJ Empresa',
            'setor_nome'    => 'Nome Setor',
            'matricula'     => 'Matrícula',
        ];

        $opcoesOrdenacao = [
            'id'            => 'ID',
            'nome'          => 'Nome',
            'empresa_nome'  => 'Nome Empresa',
            'empresa_cnpj'  => 'CNPJ Empresa',
            'setor_nome'    => 'Nome Setor',
            'matricula'     => 'Matrícula',
        ];

        $campo         = $validados['campo']        ?? 'id';
        $busca         = $validados['busca']        ?? null;
        $ordenarPor    = $validados['ordenar_por']  ?? 'id';
        $direcao       = $validados['direcao']      ?? 'asc';
        $ativo         = $validados['ativo']        ?? 'todos';
        $terceirizado  = $validados['terceirizado'] ?? 'todos';

        $mapaOrdenacao = [
            'id'           => 'funcionarios.id',
            'nome'         => 'funcionarios.nome',
            'matricula'    => 'funcionarios.matricula',
            'empresa_nome' => 'empresas.nome_fantasia',
            'empresa_cnpj' => 'empresas.cnpj',
            'setor_nome'   => 'setores.nome',
        ];

        $consulta = Funcionario::query()
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
                // Busca por nome OU sobrenome
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

        // Ordenação
        if ($ordenarPor === 'nome') {
            // Ordena por nome e, em caso de empate, por sobrenome
            $consulta->orderBy('funcionarios.nome', $direcao)
                ->orderBy('funcionarios.sobrenome', $direcao);
        } else {
            $colunaOrdenacao = Arr::get($mapaOrdenacao, $ordenarPor, 'funcionarios.id');
            $consulta->orderBy($colunaOrdenacao, $direcao);
        }

        $funcionarios = $consulta->paginate(10)->appends($validados);

        return view('funcionarios.index', [
            'funcionarios'     => $funcionarios,
            'opcoesCampo'      => $opcoesCampo,
            'opcoesOrdenacao'  => $opcoesOrdenacao,
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

        $dados['cpf'] = Mask::digits($dados['cpf'] ?? '', 11);
        if (!empty($dados['telefone'])) {
            $dados['telefone'] = Mask::digits($dados['telefone'], 11);
        }

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
        $funcionario->with([
            'setor:id,nome,empresa_id',
            'setor.empresa:id,nome_fantasia,cnpj',
        ]);

        return view('funcionarios.show', compact('funcionario'));
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

        // Bools (checkboxes)
        $dados['terceirizado'] = $request->boolean('terceirizado');
        $dados['ativo']        = $request->boolean('ativo');

        // Regras para desligado_em
        if (array_key_exists('ativo', $dados)) {
            if ($dados['ativo'] === false && $funcionario->ativo === true) {
                // inativando agora
                $dados['desligado_em'] = now();
            } elseif ($dados['ativo'] === true) {
                // reativando: zera desligado_em (ajuste se preferir manter histórico)
                $dados['desligado_em'] = null;
            }
        }

        // Não persistimos empresa_id (ela é inferida pelo setor)
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

        if (! $funcionario->ativo && $funcionario->trashed()) {
            return redirect()->route('funcionarios.index')
                ->with('error', 'O funcionário já está inativo/excluído.');
        }

        $funcionario->ativo = false;
        $funcionario->save();
        $funcionario->delete();

        return redirect()->route('funcionarios.index')
            ->with('success', 'Funcionário inativado e removido com sucesso.');
    }
}
