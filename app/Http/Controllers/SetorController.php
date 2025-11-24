<?php

namespace App\Http\Controllers;

use App\Http\Requests\Setores\IndexRequest;
use App\Http\Requests\Setores\StoreSetorRequest;
use App\Http\Requests\Setores\UpdateSetorRequest;
use App\Models\Empresa;
use App\Models\Setor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SetorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        $dadosValidados   = $request->validated();

        $nome             = $dadosValidados['nome']        ?? null;
        $empresaId        = $dadosValidados['empresa_id']  ?? null;
        $ativo            = $dadosValidados['ativo']       ?? null;
        $ordenarPor       = $dadosValidados['ordenar_por'] ?? 'id';
        $direcao          = ($dadosValidados['direcao'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
        $itensPorPagina   = 25;

        $ordenaveis = ['id', 'nome', 'empresa_id', 'nome_empresa', 'cnpj_empresa', 'ativo'];
        if (! in_array($ordenarPor, $ordenaveis, true)) {
            $ordenarPor = 'id';
        }

        $mapaOrdenacao = [
            'id'            => 'setores.id',
            'nome'          => 'setores.nome',
            'empresa_id'    => 'setores.empresa_id',
            'ativo'         => 'setores.ativo',
            'nome_empresa'  => 'empresas.razao_social',
            'cnpj_empresa'  => 'empresas.cnpj',
        ];

        $query = Setor::query()
            ->select('setores.*')
            ->with(['empresa:id,razao_social,cnpj'])

            ->when(in_array($ordenarPor, ['nome_empresa', 'cnpj_empresa'], true), function ($q) {
                $q->leftJoin('empresas', 'empresas.id', '=', 'setores.empresa_id');
            })

            ->when(!empty($nome), fn($q) => $q->where('setores.nome', 'like', "%{$nome}%"))
            ->when($empresaId !== null && $empresaId !== '', fn($q) => $q->where('setores.empresa_id', (int) $empresaId))
            ->when($ativo !== null && $ativo !== '', fn($q) => $q->where('setores.ativo', (int) $ativo))
            ->orderBy($mapaOrdenacao[$ordenarPor], $direcao);

        $setores = $query->paginate($itensPorPagina)->withQueryString();

        $opcoesEmpresas = Empresa::orderBy('id')->get(['id', 'razao_social', 'cnpj']);

        return view('setores.index', [
            'setores'        => $setores,
            'opcoesEmpresas' => $opcoesEmpresas,
            'ordenarPor'     => $ordenarPor,
            'direcao'        => $direcao,
            'nome'           => $nome,
            'empresaId'      => $empresaId,
            'ativo'          => $ativo,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $empresas = Empresa::query()
            ->select('id', 'cnpj', 'razao_social')
            ->when(Schema::hasColumn('empresas', 'ativo'), fn($q) => $q->where('ativo', true))
            ->when(Schema::hasColumn('empresas', 'apagado_em'), fn($q) => $q->whereNull('apagado_em'))
            ->orderBy('id')
            ->get();

        return view('setores.create', compact('empresas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSetorRequest $request)
    {
        $data = $request->validated();
        $setor = Setor::create($data);

        return to_route('setores.index')
            ->with('success', 'Setor criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Setor $setor)
    {
        $setor->with(['empresa:id,razao_social,cnpj']);
        return view('setores.show', compact('setor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Setor $setor)
    {
        $opcoesEmpresas = Empresa::orderBy('id')->get(['id', 'razao_social', 'cnpj']);
        return view('setores.edit', compact('setor', 'opcoesEmpresas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSetorRequest $request, Setor $setor)
    {
        $dadosValidados   = $request->validated();

        $setor->update($dadosValidados);

        return to_route('setores.index')
            ->with('success', 'Setor atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setor $setor)
    {
        $setor->loadCount([
            'funcionarios',
            'funcionarios as funcionarios_ativos_count' => fn($q) => $q->where('ativo', 1)
        ]);

        if ($setor->funcionarios_count > 0) {
            $msg = "Não é possível excluir: há {$setor->funcionarios_count} funcionário(s) cadastrado(s) no setor";
            if ($setor->funcionarios_ativos_count > 0) {
                $msg .= " ({$setor->funcionarios_ativos_count} ativo(s)).";
            } else {
                $msg .= ".";
            }
            return back()->with('error', $msg);
        }

        try {
            $setor->delete();

            return to_route('setores.index')
                ->with('success', 'Setor excluído com sucesso!');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Erro ao excluir o setor. Tente novamente.');
        }
    }
}
