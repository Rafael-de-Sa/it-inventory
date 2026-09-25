<?php

namespace Tests\Feature\Movimentacoes;

use App\Models\Equipamento;
use App\Models\Funcionario;
use App\Models\Movimentacao;
use App\Models\MovimentacaoEquipamento;
use App\Models\Ocorrencia;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Termo de troca (issue #10): o equipamento em uso é devolvido e o substituto é entregue ao mesmo funcionário.
 */
class TrocaTest extends TestCase
{
    use RefreshDatabase;

    private Funcionario $funcionario;

    private Movimentacao $termo;

    private Equipamento $antigo;

    private Equipamento $novo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->autenticar();

        $this->funcionario = Funcionario::factory()->create();
        $this->antigo = Equipamento::factory()->create(['identificacao' => 'CELUR01', 'fabricante' => 'Samsung', 'modelo' => 'Galaxy A03 Core']);
        $this->novo = Equipamento::factory()->create(['fabricante' => 'Samsung', 'modelo' => 'Galaxy A05s']);

        $this->post(route('movimentacoes.store'), [
            'empresa_id' => $this->funcionario->setor->empresa_id,
            'setor_id' => $this->funcionario->setor_id,
            'funcionario_id' => $this->funcionario->id,
            'equipamentos' => [$this->antigo->id],
        ])->assertSessionHasNoErrors();

        $this->termo = Movimentacao::sole();
    }

    private function trocar(array $dados = [])
    {
        return $this->post(route('movimentacoes.troca.store'), array_merge([
            'equipamento_antigo_id' => $this->antigo->id,
            'equipamento_novo_id' => $this->novo->id,
            'tipo_troca' => 'interna',
            'motivo' => 'defeito',
            'transferir_identificacao' => '1',
            'observacao' => 'Erro SecStatus 0x2014',
        ], $dados));
    }

    private function troca(): Movimentacao
    {
        return Movimentacao::where('tipo_movimentacao', Movimentacao::TIPO_TROCA)->sole();
    }

    public function test_troca_interna_devolve_o_antigo_e_entrega_o_substituto_ao_mesmo_funcionario(): void
    {
        $this->trocar()->assertSessionHasNoErrors();

        $troca = $this->troca();
        $this->assertSame($this->funcionario->id, $troca->funcionario_id);
        $this->assertSame('interna', $troca->tipo_troca);
        $this->assertSame('pendente', $troca->status);

        $itemAntigo = MovimentacaoEquipamento::where('equipamento_id', $this->antigo->id)->sole();
        $this->assertNotNull($itemAntigo->devolvido_em);
        $this->assertSame($troca->id, $itemAntigo->devolucao_movimentacao_id);
        $this->assertSame('defeito', $itemAntigo->motivo_devolucao);

        $itemNovo = MovimentacaoEquipamento::where('equipamento_id', $this->novo->id)->sole();
        $this->assertSame($troca->id, $itemNovo->movimentacao_id);
        $this->assertSame($itemAntigo->id, $itemNovo->substitui_item_id);
        $this->assertNull($itemNovo->devolvido_em);

        $this->assertSame('defeituoso', $this->antigo->fresh()->status);
        $this->assertSame('em_uso', $this->novo->fresh()->status);
        $this->assertSame($itemNovo->id, $this->novo->fresh()->emprestimoEmAberto()?->id);

        // O termo original só tinha o equipamento trocado: fica encerrado.
        $this->assertSame('encerrada', $this->termo->fresh()->status);
    }

    public function test_troca_pelo_fornecedor_da_baixa_no_antigo(): void
    {
        // O motivo é ignorado na troca pelo fornecedor.
        $this->trocar(['tipo_troca' => 'fornecedor', 'motivo' => 'manutencao'])->assertSessionHasNoErrors();

        $this->assertSame('baixado', $this->antigo->fresh()->status);
        $this->assertSame('troca_fornecedor', MovimentacaoEquipamento::where('equipamento_id', $this->antigo->id)->value('motivo_devolucao'));
        $this->assertSame('fornecedor', $this->troca()->tipo_troca);
    }

    public function test_transfere_a_identificacao_interna_para_o_substituto(): void
    {
        $this->trocar()->assertSessionHasNoErrors();

        $this->assertNull($this->antigo->fresh()->identificacao);
        $this->assertSame('CELUR01', $this->novo->fresh()->identificacao);

        $evento = $this->antigo->historicos()->where('evento', 'troca')->sole();
        $this->assertStringContainsString('Identificação CELUR01 transferida', $evento->observacao);
        $this->assertSame($this->troca()->id, $evento->movimentacao_id);
        $this->assertSame('troca', $this->novo->historicos()->latest('id')->value('evento'));
    }

    public function test_sem_transferencia_cada_equipamento_mantem_a_sua_identificacao(): void
    {
        $this->trocar(['transferir_identificacao' => '0'])->assertSessionHasNoErrors();

        $this->assertSame('CELUR01', $this->antigo->fresh()->identificacao);
        $this->assertNull($this->novo->fresh()->identificacao);
    }

    public function test_nao_transfere_se_o_substituto_ja_tem_identificacao(): void
    {
        $this->novo->update(['identificacao' => 'CELUR99']);

        $this->trocar()->assertSessionHasErrors('transferir_identificacao');
        $this->assertSame('em_uso', $this->antigo->fresh()->status);
    }

    public function test_so_troca_equipamento_em_uso_por_um_disponivel(): void
    {
        $semUso = Equipamento::factory()->create();
        $this->trocar(['equipamento_antigo_id' => $semUso->id])->assertSessionHasErrors('equipamento_antigo_id');

        $emManutencao = Equipamento::factory()->create(['status' => 'em_manutencao']);
        $this->trocar(['equipamento_novo_id' => $emManutencao->id])->assertSessionHasErrors('equipamento_novo_id');

        $inativo = Equipamento::factory()->create(['ativo' => false]);
        $this->trocar(['equipamento_novo_id' => $inativo->id])->assertSessionHasErrors('equipamento_novo_id');

        $this->trocar(['equipamento_novo_id' => $this->antigo->id])->assertSessionHasErrors('equipamento_novo_id');

        $this->assertSame(0, Movimentacao::where('tipo_movimentacao', Movimentacao::TIPO_TROCA)->count());
    }

    public function test_troca_interna_exige_a_condicao_do_antigo(): void
    {
        $this->trocar(['motivo' => ''])->assertSessionHasErrors('motivo');
    }

    public function test_substituto_pode_ser_devolvido_depois_e_encerra_a_troca(): void
    {
        $this->trocar()->assertSessionHasNoErrors();

        $this->getJson(route('movimentacoes.equipamentos-em-uso', $this->funcionario))
            ->assertOk()->assertJsonPath('0.id', $this->novo->id);

        $this->post(route('movimentacoes.devolucao.store'), [
            'empresa_id' => $this->funcionario->setor->empresa_id,
            'setor_id' => $this->funcionario->setor_id,
            'funcionario_id' => $this->funcionario->id,
            'equipamentos' => [$this->novo->id],
            'motivos_devolucao_equipamentos' => [$this->novo->id => 'devolucao'],
        ])->assertSessionHasNoErrors();

        $this->assertSame('disponivel', $this->novo->fresh()->status);
        $this->assertSame('encerrada', $this->troca()->status);
    }

    public function test_termo_de_troca_e_envio_do_termo_assinado(): void
    {
        Storage::fake('local');
        $this->trocar()->assertSessionHasNoErrors();
        $troca = $this->troca();

        $this->get(route('movimentacoes.show', $troca))->assertOk()
            ->assertSee('Termo de troca')->assertSee('Galaxy A05s')->assertSee('Galaxy A03 Core');

        $this->get(route('movimentacoes.termo-troca', $troca))
            ->assertOk()->assertHeader('Content-Type', 'application/pdf');

        // Termo de outro tipo não gera o termo de troca.
        $this->get(route('movimentacoes.termo-troca', $this->termo))->assertNotFound();

        $this->post(route('movimentacoes.upload-termo-responsabilidade', $troca), [
            'arquivo_termo' => UploadedFile::fake()->create('termo.pdf', 100, 'application/pdf'),
        ])->assertSessionHasNoErrors();

        $this->assertSame('concluida', $troca->fresh()->status);
        $this->assertNotNull($troca->fresh()->termo_responsabilidade);
    }

    public function test_termo_de_troca_pendente_bloqueia_o_desligamento(): void
    {
        $this->termo->update(['termo_responsabilidade' => 'termos/responsabilidade-teste.pdf']);
        $this->trocar()->assertSessionHasNoErrors();

        $restricoes = $this->funcionario->fresh()->obterRestricoesDesligamento();

        $this->assertTrue($restricoes['equipamentos_em_uso']);
        $this->assertTrue($restricoes['termos_responsabilidade_pendentes']);
    }

    public function test_troca_a_partir_da_ocorrencia_a_vincula(): void
    {
        $ocorrencia = Ocorrencia::factory()->create(['equipamento_id' => $this->antigo->id]);

        $this->get(route('movimentacoes.troca.create', ['ocorrencia_id' => $ocorrencia->id]))
            ->assertOk()->assertSee('Troca da ocorrência #' . $ocorrencia->id);

        $this->trocar(['ocorrencia_id' => $ocorrencia->id])->assertSessionHasNoErrors();

        // A troca resolve a ocorrência.
        $ocorrencia->refresh();
        $this->assertSame($this->troca()->id, $ocorrencia->troca_movimentacao_id);
        $this->assertStringContainsString('Troca do equipamento', $ocorrencia->solucao);
        $this->assertFalse($ocorrencia->estaAberta());
        $this->assertSame(today()->format('Y-m-d'), $ocorrencia->liberado_em->format('Y-m-d'));

        // Ocorrência de outro equipamento não pode ser vinculada.
        $outra = Ocorrencia::factory()->create();
        $this->novo->refresh();
        $substituto = Equipamento::factory()->create();
        $this->trocar([
            'equipamento_antigo_id' => $this->novo->id,
            'equipamento_novo_id' => $substituto->id,
            'transferir_identificacao' => '0',
            'ocorrencia_id' => $outra->id,
        ])->assertSessionHasErrors('ocorrencia_id');
    }

    public function test_perfil_dp_nao_registra_troca(): void
    {
        $this->autenticar(Usuario::factory()->departamentoPessoal()->create());

        $this->get(route('movimentacoes.troca.create'))->assertForbidden();
        $this->trocar()->assertForbidden();
    }
}
