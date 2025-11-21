<?php

namespace App\Http\Controllers;

use App\Http\Requests\Equipamentos\IndexRequest;
use App\Http\Requests\Equipamentos\StoreEquipamentoRequest;
use App\Http\Requests\Equipamentos\UpdateEquipamentoRequest;
use App\Models\Equipamento;
use App\Models\TipoEquipamento;
use Illuminate\Support\Str;

class EquipamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        $campoFiltro       = $request->input('campo', '');
        $termoBusca        = $request->input('busca', '');
        $ordenarPor        = $request->input('ordenar_por', 'id');
        $direcaoOrdenacao  = $request->input('direcao', 'asc');
        $statusFiltro      = $request->input('status', 'todos');

        $consulta = Equipamento::query()
            ->with('tipoEquipamento')
            ->leftJoin('tipo_equipamentos', 'tipo_equipamentos.id', '=', 'equipamentos.tipo_equipamento_id')
            ->select('equipamentos.*');

        if ($statusFiltro && $statusFiltro !== 'todos') {
            $consulta->where('equipamentos.status', $statusFiltro);
        }

        if ($termoBusca !== null && $termoBusca !== '') {
            $buscaLike = '%' . Str::of($termoBusca)->trim() . '%';

            switch ($campoFiltro) {
                case 'id':
                    $consulta->where('equipamentos.id', (int) $termoBusca);
                    break;
                case 'tipo':
                    $consulta->where('tipo_equipamentos.nome', 'like', $buscaLike);
                    break;
                case 'descricao':
                    $consulta->where('equipamentos.descricao', 'like', $buscaLike);
                    break;
                case 'patrimonio':
                    $consulta->where('equipamentos.patrimonio', 'like', $buscaLike);
                    break;
                case 'numero_serie':
                    $consulta->where('equipamentos.numero_serie', 'like', $buscaLike);
                    break;
                case 'status':
                    $consulta->where('equipamentos.status', 'like', $buscaLike);
                    break;
                default:
                    $consulta->where(function ($q) use ($termoBusca, $buscaLike) {
                        $q->orWhere('equipamentos.id', (int) $termoBusca)
                            ->orWhere('tipo_equipamentos.nome', 'like', $buscaLike)
                            ->orWhere('equipamentos.descricao', 'like', $buscaLike)
                            ->orWhere('equipamentos.patrimonio', 'like', $buscaLike)
                            ->orWhere('equipamentos.numero_serie', 'like', $buscaLike)
                            ->orWhere('equipamentos.status', 'like', $buscaLike);
                    });
                    break;
            }
        }

        switch ($ordenarPor) {
            case 'tipo':
                $consulta->orderBy('tipo_equipamentos.nome', $direcaoOrdenacao);
                break;
            case 'patrimonio':
                $consulta->orderBy('equipamentos.patrimonio', $direcaoOrdenacao);
                break;
            case 'numero_serie':
                $consulta->orderBy('equipamentos.numero_serie', $direcaoOrdenacao);
                break;
            case 'status':
                $consulta->orderBy('equipamentos.status', $direcaoOrdenacao);
                break;
            default:
                $consulta->orderBy('equipamentos.id', $direcaoOrdenacao);
                break;
        }

        $listaDeEquipamentos = $consulta->paginate(25)->withQueryString();

        return view('equipamentos.index', compact(
            'listaDeEquipamentos',
            'campoFiltro',
            'termoBusca',
            'ordenarPor',
            'direcaoOrdenacao',
            'statusFiltro'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Tipos ativos e não arquivados, ordenados por nome (padrão dos cadastros)
        $opcoesTipos = TipoEquipamento::query()
            ->where('ativo', true)
            ->whereNull('apagado_em')
            ->orderBy('nome')
            ->pluck('nome', 'id');

        // Lista de status conforme enum da migration
        $listaStatus = [
            'disponivel'     => 'Disponível',
            'em_manutencao'  => 'Em manutenção',
            'defeituoso'     => 'Defeituoso',
            'descartado'     => 'Descartado',
        ];

        return view('equipamentos.create', compact('opcoesTipos', 'listaStatus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEquipamentoRequest $request)
    {
        $dadosValidados = $request->validated();

        $equipamento = Equipamento::create([
            'tipo_equipamento_id' => $dadosValidados['tipo_equipamento_id'],
            'data_compra'         => $dadosValidados['data_compra'] ?? null,
            'valor_compra'        => $dadosValidados['valor_compra'] ?? null,
            'status'              => $dadosValidados['status'],
            'descricao'           => $dadosValidados['descricao'] ?? null,
            'patrimonio'          => $dadosValidados['patrimonio'] ?? null,
            'numero_serie'        => $dadosValidados['numero_serie'] ?? null,
        ]);

        return redirect()
            ->route('equipamentos.index')
            ->with('success', 'Equipamento cadastrado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Equipamento $equipamento)
    {
        $equipamento->load('tipoEquipamento:id,nome');

        return view('equipamentos.show', compact('equipamento'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Equipamento $equipamento)
    {
        $opcoesTiposEquipamento = TipoEquipamento::orderBy('nome')->pluck('nome', 'id');

        return view('equipamentos.edit', compact('equipamento', 'opcoesTiposEquipamento'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEquipamentoRequest $request, Equipamento $equipamento)
    {
        $dados = $request->validated();

        foreach (['data_compra', 'valor_compra', 'descricao', 'patrimonio', 'numero_serie'] as $k) {
            if (($dados[$k] ?? '') === '') $dados[$k] = null;
        }

        if (!empty($dados['numero_serie'])) {
            $dados['numero_serie'] = mb_strtoupper(trim($dados['numero_serie']));
        }

        $equipamento->fill([
            'tipo_equipamento_id' => $dados['tipo_equipamento_id'],
            'data_compra'         => $dados['data_compra'] ?? null,
            'valor_compra'        => $dados['valor_compra'] ?? null,
            'status'              => $dados['status'],
            'descricao'           => $dados['descricao'] ?? null,
            'patrimonio'          => $dados['patrimonio'] ?? null,
            'numero_serie'        => $dados['numero_serie'] ?? null,
        ])->save();

        return redirect()
            ->route('equipamentos.index')
            ->with('success', 'Equipamento atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Equipamento $equipamento)
    {
        $temMovimentacaoAberta = $equipamento->movimentacoes()
            ->whereNull('devolvido_em')
            ->whereNull('termo_devolucao')
            ->exists();

        if ($temMovimentacaoAberta) {
            return back()->with(
                'error',
                'Não é possível excluir: este equipamento possui movimentação pendente (aguardando devolução com termo).'
            );
        }

        $equipamento->delete();

        return redirect()
            ->route('equipamentos.index')
            ->with('success', 'Equipamento excluído com sucesso.');
    }
}
