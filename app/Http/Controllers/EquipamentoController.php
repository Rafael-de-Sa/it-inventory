<?php

namespace App\Http\Controllers;

use App\Enums\CategoriaEquipamento;
use App\Http\Requests\Equipamentos\EquipamentoRequest;
use App\Http\Requests\Equipamentos\IndexRequest;
use App\Http\Requests\Equipamentos\StoreEquipamentoRequest;
use App\Http\Requests\Equipamentos\UpdateEquipamentoRequest;
use App\Models\Equipamento;
use App\Models\TipoEquipamento;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
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
                case 'equipamento':
                    $consulta->where(fn ($q) => $this->filtrarPorNome($q, $buscaLike));
                    break;
                case 'identificacao':
                    $consulta->where('equipamentos.identificacao', 'like', $buscaLike);
                    break;
                case 'imei_mac':
                    $consulta->where(fn ($q) => $this->filtrarPorImeiMac($q, $buscaLike));
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
                            ->orWhere(fn ($nome) => $this->filtrarPorNome($nome, $buscaLike))
                            ->orWhere('equipamentos.identificacao', 'like', $buscaLike)
                            ->orWhere('equipamentos.patrimonio', 'like', $buscaLike)
                            ->orWhere('equipamentos.numero_serie', 'like', $buscaLike)
                            ->orWhere(fn ($rede) => $this->filtrarPorImeiMac($rede, $buscaLike))
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
        $tipos = TipoEquipamento::query()
            ->where('ativo', true)
            ->orderBy('nome')
            ->get(['id', 'nome', 'categoria']);

        $listaStatus = Arr::only(Equipamento::STATUS, Equipamento::STATUS_CADASTRO);

        return view('equipamentos.create', [
            'equipamento' => null,
            'tipos' => $tipos,
            'listaStatus' => $listaStatus,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEquipamentoRequest $request)
    {
        DB::transaction(function () use ($request) {
            $equipamento = Equipamento::create(Arr::only($request->validated(), self::CAMPOS_COMUNS));

            $this->salvarFichaTecnica($equipamento, $request);
        });

        return redirect()
            ->route('equipamentos.index')
            ->with('success', 'Equipamento cadastrado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Equipamento $equipamento)
    {
        $equipamento->load([
            'tipoEquipamento', 'computador', 'monitor', 'impressora', 'dispositivoMovel',
            'ocorrencias' => fn ($consulta) => $consulta->latest('reportado_em')->latest('id'),
        ]);

        return view('equipamentos.show', compact('equipamento'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Equipamento $equipamento)
    {
        $equipamento->load(['tipoEquipamento', 'computador', 'monitor', 'impressora', 'dispositivoMovel']);

        // Inclui o tipo atual mesmo que tenha sido inativado.
        $tipos = TipoEquipamento::query()
            ->where(fn ($query) => $query->where('ativo', true)->orWhere('id', $equipamento->tipo_equipamento_id))
            ->orderBy('nome')
            ->get(['id', 'nome', 'categoria']);

        $emprestimoEmAberto = $equipamento->emprestimoEmAberto();

        return view('equipamentos.edit', compact('equipamento', 'tipos', 'emprestimoEmAberto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEquipamentoRequest $request, Equipamento $equipamento)
    {
        DB::transaction(function () use ($request, $equipamento) {
            $equipamento->fill(Arr::only($request->validated(), self::CAMPOS_COMUNS))->save();

            $this->salvarFichaTecnica($equipamento->refresh(), $request);
        });

        return redirect()
            ->route('equipamentos.index')
            ->with('success', 'Equipamento atualizado com sucesso.');
    }

    /** Fabricante, modelo ou descrição (equipamentos anteriores à 2.0 só têm descrição). */
    private function filtrarPorNome($consulta, string $buscaLike): void
    {
        $consulta->where('equipamentos.fabricante', 'like', $buscaLike)
            ->orWhere('equipamentos.modelo', 'like', $buscaLike)
            ->orWhereRaw("CONCAT_WS(' ', equipamentos.fabricante, equipamentos.modelo) LIKE ?", [$buscaLike])
            ->orWhere('equipamentos.descricao', 'like', $buscaLike);
    }

    /**
     * IMEI ou MAC em qualquer ficha técnica. O MAC é buscado com ou sem separadores, e só quando o termo
     * parece um trecho de MAC (apenas hexadecimais e separadores, ao menos 4 dígitos): assim "Dell" não
     * encontra MACs que contenham "DE".
     */
    private function filtrarPorImeiMac($consulta, string $buscaLike): void
    {
        $termo = trim($buscaLike, '%');
        $hex = preg_replace('/[:\-.\s]/', '', $termo);
        $pareceMac = strlen($hex) >= 4 && ctype_xdigit($hex);
        $mac = fn (string $coluna) => ["REPLACE({$coluna}, ':', '') LIKE ?", ['%' . $hex . '%']];

        $consulta->whereHas('dispositivoMovel', fn ($q) => $q->where('imei_1', 'like', $buscaLike)
                ->orWhere('imei_2', 'like', $buscaLike)
                ->when($pareceMac, fn ($q) => $q->orWhereRaw(...$mac('mac'))))
            ->when($pareceMac, fn ($q) => $q
                ->orWhereHas('computador', fn ($c) => $c->whereRaw(...$mac('mac_ethernet'))->orWhereRaw(...$mac('mac_wifi')))
                ->orWhereHas('impressora', fn ($i) => $i->whereRaw(...$mac('mac'))));
    }

    /** Campos da tabela equipamentos preenchidos pelo formulário. */
    private const CAMPOS_COMUNS = [
        'tipo_equipamento_id', 'fabricante', 'modelo', 'identificacao', 'numero_serie', 'patrimonio',
        'data_compra', 'valor_compra', 'nota_fiscal', 'chave_acesso_nf', 'status', 'descricao',
    ];

    /**
     * Grava a ficha técnica da categoria do tipo e remove a de outra categoria
     * (quando o tipo do equipamento foi trocado por um de categoria diferente).
     */
    private function salvarFichaTecnica(Equipamento $equipamento, EquipamentoRequest $request): void
    {
        $categoriaAtual = $request->categoria();

        foreach (CategoriaEquipamento::cases() as $categoria) {
            $relacao = $categoria->relacao();

            if ($relacao && $categoria !== $categoriaAtual) {
                $equipamento->{$relacao}()->delete();
            }
        }

        if ($relacao = $categoriaAtual?->relacao()) {
            $equipamento->{$relacao}()->updateOrCreate([], $request->fichaTecnica());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Equipamento $equipamento)
    {
        // Mesma regra que trava o status "Em uso": item de termo de responsabilidade ainda não devolvido.
        if ($emprestimo = $equipamento->emprestimoEmAberto()) {
            return back()->with(
                'error',
                "Não é possível excluir: o equipamento está em uso pela movimentação #{$emprestimo->movimentacao_id}. Registre a devolução antes."
            );
        }

        $equipamento->delete();

        return redirect()
            ->route('equipamentos.index')
            ->with('success', 'Equipamento excluído com sucesso.');
    }
}
