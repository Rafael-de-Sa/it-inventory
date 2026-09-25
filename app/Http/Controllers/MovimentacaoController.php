<?php

namespace App\Http\Controllers;

use App\Http\Requests\Movimentacoes\IndexRequest;
use App\Http\Requests\Movimentacoes\StoreDevolucaoMovimentacaoRequest;
use App\Http\Requests\Movimentacoes\StoreMovimentacaoRequest;
use App\Http\Requests\Movimentacoes\UploadTermoDevolucaoRequest;
use App\Http\Requests\Movimentacoes\UploadTermoResponsabilidadeRequest;
use App\Models\Empresa;
use App\Models\Equipamento;
use App\Models\Funcionario;
use App\Models\Movimentacao;
use App\Models\MovimentacaoEquipamento;
use App\Models\Setor;
use App\Services\RegistroDevolucao;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MovimentacaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        $dados = $request->validated();

        $query = Movimentacao::query()
            ->with([
                'setor.empresa',
                'funcionario',
                'equipamentos',
            ])
            ->whereNull('apagado_em');

        if (!empty($dados['busca'])) {
            $busca = trim($dados['busca']);

            if (ctype_digit($busca)) {
                $query->where('id', (int) $busca);
            } else {
            }
        }

        if (!empty($dados['empresa_id'])) {
            $empresaId = (int) $dados['empresa_id'];

            $query->whereHas('setor', function ($subQuery) use ($empresaId) {
                $subQuery->where('empresa_id', $empresaId)
                    ->whereNull('apagado_em');
            });
        }

        if (!empty($dados['setor_id']) && !empty($dados['empresa_id'])) {
            $query->where('setor_id', (int) $dados['setor_id']);
        }

        if (!empty($dados['funcionario_id']) && !empty($dados['empresa_id']) && !empty($dados['setor_id'])) {
            $query->where('funcionario_id', (int) $dados['funcionario_id']);
        }

        if (!empty($dados['status'])) {
            $query->where('status', $dados['status']);
        }

        $colunaOrdenacao  = $dados['ordenar_por'] ?? 'id';
        $direcaoOrdenacao = $dados['direcao'] ?? 'asc';

        switch ($colunaOrdenacao) {
            case 'id':
                $colunaBanco = 'id';
                break;

            case 'status':
                $colunaBanco = 'status';
                break;

            case 'data':
            default:
                $colunaBanco  = 'criado_em';
                $colunaOrdenacao = 'data';
                break;
        }

        $query->orderBy($colunaBanco, $direcaoOrdenacao);

        $listaDeMovimentacoes = $query
            ->paginate(25)
            ->withQueryString();

        $listaDeEmpresas = Empresa::query()
            ->aptasParaMovimentacao()
            ->orderBy('razao_social')
            ->orderBy('nome_fantasia')
            ->get();

        $listaDeSetores = collect();
        if (!empty($dados['empresa_id'])) {
            $listaDeSetores = Setor::query()
                ->where('empresa_id', (int) $dados['empresa_id'])
                ->whereNull('apagado_em')
                ->orderBy('nome')
                ->get();
        }

        $listaDeFuncionarios = collect();
        if (!empty($dados['setor_id'])) {
            $listaDeFuncionarios = Funcionario::query()
                ->where('setor_id', (int) $dados['setor_id'])
                ->whereNull('apagado_em')
                ->orderBy('nome')
                ->orderBy('sobrenome')
                ->get();
        }

        $termoBusca = $dados['busca'] ?? null;

        return view('movimentacoes.index', [
            'listaDeMovimentacoes' => $listaDeMovimentacoes,
            'listaDeEmpresas'      => $listaDeEmpresas,
            'listaDeSetores'       => $listaDeSetores,
            'listaDeFuncionarios'  => $listaDeFuncionarios,
            'termoBusca'           => $termoBusca,
            'colunaOrdenacao'      => $colunaOrdenacao,
            'direcaoOrdenacao'     => $direcaoOrdenacao,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $listaDeEmpresas = Empresa::query()
            ->aptasParaMovimentacao()
            ->orderBy('razao_social')
            ->orderBy('nome_fantasia')
            ->get();

        $listaDeEquipamentos = Equipamento::query()
            ->where('ativo', true)
            ->whereNull('apagado_em')
            ->where('status', 'disponivel')
            ->orderBy('id')
            ->get();

        return view('movimentacoes.create', compact('listaDeEmpresas', 'listaDeEquipamentos'));
    }

    public function createDevolucao()
    {
        $listaDeEmpresas = Empresa::query()
            ->aptasParaMovimentacao()
            ->orderBy('razao_social')
            ->orderBy('nome_fantasia')
            ->get();

        return view('movimentacoes.devolucao.create', compact('listaDeEmpresas'));
    }

    public function store(StoreMovimentacaoRequest $request)
    {
        $dadosValidados = $request->validated();

        DB::transaction(function () use ($dadosValidados) {
            $movimentacao = Movimentacao::create([
                'setor_id'       => $dadosValidados['setor_id'],
                'funcionario_id' => $dadosValidados['funcionario_id'],
                'observacao'     => $dadosValidados['observacao'] ?? null,
                'tipo_movimentacao' => Movimentacao::TIPO_RESPONSABILIDADE,
            ]);

            $idsEquipamentos = $dadosValidados['equipamentos'];

            $equipamentos = Equipamento::query()
                ->whereIn('id', $idsEquipamentos)
                ->lockForUpdate()
                ->get();

            foreach ($equipamentos as $equipamento) {
                $movimentacao->equipamentos()->attach($equipamento->id);

                $equipamento->comHistorico('emprestimo', $movimentacao)->update([
                    'status' => 'em_uso',
                ]);
            }
        });

        return redirect()
            ->route('movimentacoes.index')
            ->with('success', 'Movimentação gerada com sucesso.');
    }

    public function storeDevolucao(StoreDevolucaoMovimentacaoRequest $request)
    {
        $dados = $request->validated();
        $motivoGeral = $dados['motivo_devolucao'] ?? 'devolucao';

        $itens = collect($dados['equipamentos'])->mapWithKeys(fn ($equipamentoId) => [
            (int) $equipamentoId => [
                'motivo' => $dados['motivos_devolucao_equipamentos'][$equipamentoId] ?? $motivoGeral,
                'observacao' => $dados['observacoes_equipamentos'][$equipamentoId] ?? null,
            ],
        ])->all();

        RegistroDevolucao::registrar($dados['setor_id'], $dados['funcionario_id'], $itens, $dados['observacao'] ?? null);

        return redirect()
            ->route('movimentacoes.index')
            ->with('success', 'Devolução registrada com sucesso.');
    }


    /**
     * Display the specified resource.
     */
    public function show(Movimentacao $movimentacao)
    {
        $movimentacao->load([
            'setor.empresa',
            'funcionario',
            'equipamentos.tipoEquipamento',
        ]);

        $movimentacao->setRelation(
            'equipamentos',
            $movimentacao->equipamentos->sortBy('id')->values()
        );

        if ($movimentacao->tipo_movimentacao === Movimentacao::TIPO_TROCA) {
            $movimentacao->load([
                'equipamentos' => fn ($consulta) => $consulta->with('tipoEquipamento'),
                'itensDevolvidosAqui.equipamento.tipoEquipamento',
                'ocorrencias',
            ]);

            return view('movimentacoes.troca.show', [
                'movimentacao' => $movimentacao,
            ]);
        }

        if ($movimentacao->tipo_movimentacao === Movimentacao::TIPO_RESPONSABILIDADE) {
            return view('movimentacoes.show-responsabilidade', [
                'movimentacao' => $movimentacao,
            ]);
        } else {
            return view('movimentacoes.devolucao.show', [
                'movimentacao' => $movimentacao,
            ]);
        }
    }

    public function setoresParaMovimentacao(Empresa $empresa)
    {
        $setores = $empresa->setores()
            ->where('ativo', true)
            ->whereNull('apagado_em')
            ->whereHas('funcionarios', function ($query) {
                $query->where('ativo', true)
                    ->whereNull('desligado_em')
                    ->whereNull('apagado_em');
            })
            ->orderBy('nome')
            ->get(['id', 'nome']);

        return response()->json($setores);
    }

    public function funcionariosParaMovimentacao(Setor $setor)
    {
        $funcionarios = $setor->funcionarios()
            ->where('ativo', true)
            ->whereNull('desligado_em')
            ->whereNull('apagado_em')
            ->orderBy('nome')
            ->orderBy('sobrenome')
            ->get(['id', 'nome', 'sobrenome', 'matricula', 'terceirizado']);

        $payload = $funcionarios->map(function (Funcionario $funcionario) {
            $nomeCompleto = trim(($funcionario->nome ?? '') . ' ' . ($funcionario->sobrenome ?? ''));

            $rotulo = $nomeCompleto;
            if ($funcionario->matricula) {
                $rotulo .= " ({$funcionario->matricula})";
            }
            if ($funcionario->terceirizado) {
                $rotulo .= ' - Terceirizado';
            }

            return [
                'id'     => $funcionario->id,
                'rotulo' => $rotulo,
            ];
        });

        return response()->json($payload);
    }

    public function gerarTermoResponsabilidade(Movimentacao $movimentacao)
    {
        if ($movimentacao->tipo_movimentacao !== Movimentacao::TIPO_RESPONSABILIDADE) {
            abort(404);
        }

        $movimentacao->load([
            'setor.empresa',
            'funcionario',
            'equipamentos' => fn ($consulta) => $consulta->with(['tipoEquipamento', ...Equipamento::RELACOES_FICHA]),
        ]);

        $nomeArquivo = 'termo_responsabilidade_movimentacao_' . $movimentacao->id . '.pdf';

        $pdf = Pdf::loadView('relatorios.movimentacoes.termo-responsabilidade', [
            'movimentacao' => $movimentacao,
        ])->setPaper('a4', 'portrait');

        $dompdf = $pdf->getDomPDF();
        $dompdf->render();


        $canvas = $dompdf->getCanvas();
        $canvas->page_text(
            520,
            810,
            "Página {PAGE_NUM} de {PAGE_COUNT}",
            null,
            9,
            [0.4, 0.4, 0.4]
        );

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "inline; filename={$nomeArquivo}");
    }
    public function gerarTermoDevolucao(Movimentacao $movimentacao)
    {
        if ($movimentacao->tipo_movimentacao !== Movimentacao::TIPO_DEVOLUCAO) {
            abort(404);
        }

        $movimentacao->load([
            'setor.empresa',
            'funcionario',
            'equipamentos' => fn ($consulta) => $consulta->with(['tipoEquipamento', ...Equipamento::RELACOES_FICHA]),
        ]);

        $nomeArquivo = 'termo_devolucao_movimentacao_' . $movimentacao->id . '.pdf';

        $pdf = Pdf::loadView('relatorios.movimentacoes.termo-devolucao', [
            'movimentacao' => $movimentacao,
        ])->setPaper('a4', 'portrait');

        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        $canvas = $dompdf->getCanvas();
        $canvas->page_text(
            520,
            810,
            "Página {PAGE_NUM} de {PAGE_COUNT}",
            null,
            9,
            [0.4, 0.4, 0.4]
        );

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "inline; filename={$nomeArquivo}");
    }

    public function equipamentosEmUsoParaDevolucao(Funcionario $funcionario)
    {
        $equipamentosEmUso = MovimentacaoEquipamento::query()
            ->whereNull('devolvido_em')
            ->whereHas('movimentacao', function ($query) use ($funcionario) {
                $query
                    ->where('funcionario_id', $funcionario->id)
                    ->comEmprestimo()
                    ->where('status', '!=', 'cancelada');
            })
            ->with('equipamento.tipoEquipamento')
            ->get()
            ->map(function (MovimentacaoEquipamento $pivot) {
                $equipamento = $pivot->equipamento;

                return [
                    'id'           => $equipamento->id,
                    'descricao'    => $equipamento->nome_exibicao,
                    'numero_serie' => $equipamento->numero_serie,
                    'patrimonio'   => $equipamento->patrimonio,
                    'tipo'         => $equipamento->tipoEquipamento->nome ?? null,
                ];
            });

        return response()->json($equipamentosEmUso);
    }

    public function uploadTermoResponsabilidade(
        UploadTermoResponsabilidadeRequest $request,
        Movimentacao $movimentacao
    ) {
        $arquivoTermo = $request->file('arquivo_termo');

        $pastaDestino = 'termos/responsabilidade';

        Storage::disk('local')->makeDirectory($pastaDestino);

        $idMovimentacao = (string) $movimentacao->id;
        $timestampArquivo = now()->format('Ymd_His');
        $extensaoArquivo = $arquivoTermo->getClientOriginalExtension();

        $nomeArquivo = $idMovimentacao . '_' . $timestampArquivo . '.' . $extensaoArquivo;

        $caminhoArquivo = $arquivoTermo->storeAs(
            $pastaDestino,
            $nomeArquivo,
            'local'
        );

        $movimentacao->termo_responsabilidade = $caminhoArquivo;
        $rotuloTermo = $movimentacao->tipo_movimentacao === Movimentacao::TIPO_TROCA ? 'Termo de troca' : 'Termo de responsabilidade';

        // Se todos os equipamentos já foram devolvidos, o termo continua "encerrada".
        if ($movimentacao->status === 'pendente') {
            $movimentacao->status = 'concluida';
        }

        $movimentacao->save();

        return redirect()
            ->route('movimentacoes.index', $movimentacao->id)
            ->with('success', $rotuloTermo . ' enviado com sucesso. Situação: ' . $movimentacao->status_rotulo . '.');
    }

    protected function visualizarTermoGenerico(
        Movimentacao $movimentacao,
        string $campoTermo,
        string $descricaoTermo
    ) {
        $caminhoRelativo = $movimentacao->{$campoTermo};

        if (empty($caminhoRelativo)) {
            abort(404, "Nenhum termo de {$descricaoTermo} foi armazenado para esta movimentação.");
        }

        if (! Storage::disk('local')->exists($caminhoRelativo)) {
            abort(404, "Arquivo do termo de {$descricaoTermo} não foi encontrado no sistema.");
        }

        $caminhoAbsoluto = storage_path('app/private/' . $caminhoRelativo);

        if (! file_exists($caminhoAbsoluto)) {
            abort(404, "Arquivo do termo de {$descricaoTermo} não foi encontrado no sistema.");
        }

        return response()->file($caminhoAbsoluto, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    public function visualizarTermoResponsabilidade(Movimentacao $movimentacao)
    {
        return $this->visualizarTermoGenerico(
            $movimentacao,
            'termo_responsabilidade',
            'responsabilidade'
        );
    }

    public function visualizarTermoDevolucao(Movimentacao $movimentacao)
    {
        return $this->visualizarTermoGenerico(
            $movimentacao,
            'termo_devolucao',
            'devolução'
        );
    }

    public function uploadTermoDevolucao(UploadTermoDevolucaoRequest $request, Movimentacao $movimentacao)
    {
        $arquivoTermo = $request->file('arquivo_termo');

        $pastaDestino = 'termos/devolucao';

        Storage::disk('local')->makeDirectory($pastaDestino);

        $nomeArquivo = sprintf(
            '%d_%s.%s',
            $movimentacao->id,
            now()->format('Ymd_His'),
            $arquivoTermo->getClientOriginalExtension()
        );

        $caminhoArquivo = $arquivoTermo->storeAs(
            $pastaDestino,
            $nomeArquivo,
            'local'
        );

        $movimentacao->termo_devolucao = $caminhoArquivo;
        $movimentacao->status = 'encerrada';
        $movimentacao->save();

        return redirect()
            ->route('movimentacoes.index', $movimentacao)
            ->with('success', 'Termo de devolução enviado com sucesso e movimentação encerrada.');
    }
}
