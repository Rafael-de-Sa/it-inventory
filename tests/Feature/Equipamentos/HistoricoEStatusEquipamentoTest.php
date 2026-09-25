<?php

namespace Tests\Feature\Equipamentos;

use App\Models\Equipamento;
use App\Models\EquipamentoHistorico;
use App\Models\Funcionario;
use App\Models\Movimentacao;
use App\Models\MovimentacaoEquipamento;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Linha do tempo do equipamento (EquipamentoObserver) e regras de status:
 * "Em uso" só é definido/retirado pelas movimentações.
 */
class HistoricoEStatusEquipamentoTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $usuario;

    private Funcionario $funcionario;

    protected function setUp(): void
    {
        parent::setUp();

        $this->usuario = $this->autenticar();
        $this->funcionario = Funcionario::factory()->create();
    }

    private function emprestar(array $equipamentos, ?Funcionario $funcionario = null)
    {
        $funcionario ??= $this->funcionario;

        return $this->post(route('movimentacoes.store'), [
            'empresa_id' => $funcionario->setor->empresa_id,
            'setor_id' => $funcionario->setor_id,
            'funcionario_id' => $funcionario->id,
            'equipamentos' => array_map(fn (Equipamento $e) => $e->id, $equipamentos),
        ]);
    }

    private function devolver(Equipamento $equipamento, string $motivo = 'devolucao', ?string $observacao = null)
    {
        return $this->post(route('movimentacoes.devolucao.store'), [
            'empresa_id' => $this->funcionario->setor->empresa_id,
            'setor_id' => $this->funcionario->setor_id,
            'funcionario_id' => $this->funcionario->id,
            'equipamentos' => [$equipamento->id],
            'motivos_devolucao_equipamentos' => [$equipamento->id => $motivo],
            'observacoes_equipamentos' => [$equipamento->id => $observacao],
        ]);
    }

    private function editarStatus(Equipamento $equipamento, string $status)
    {
        return $this->put(route('equipamentos.update', $equipamento), [
            'tipo_equipamento_id' => $equipamento->tipo_equipamento_id,
            'fabricante' => $equipamento->fabricante,
            'modelo' => $equipamento->modelo,
            'status' => $status,
        ]);
    }

    /** @return list<array{evento: string, anterior: ?string, novo: ?string}> */
    private function linhaDoTempo(Equipamento $equipamento): array
    {
        return $equipamento->historicos()->orderBy('id')->get()
            ->map(fn (EquipamentoHistorico $h) => ['evento' => $h->evento, 'anterior' => $h->status_anterior, 'novo' => $h->status_novo])
            ->all();
    }

    // ---------------------------------------------------------------- linha do tempo

    public function test_cadastro_registra_o_evento_com_o_usuario_responsavel(): void
    {
        $equipamento = Equipamento::factory()->create();

        $evento = $equipamento->historicos()->sole();
        $this->assertSame('cadastro', $evento->evento);
        $this->assertSame('disponivel', $evento->status_novo);
        $this->assertSame($this->usuario->id, $evento->usuario_id);
        $this->assertFalse((bool) $evento->reconstruido);
    }

    public function test_emprestimo_e_devolucao_registram_eventos_vinculados_as_movimentacoes(): void
    {
        $equipamento = Equipamento::factory()->create();

        $this->emprestar([$equipamento])->assertSessionHasNoErrors();
        $termo = Movimentacao::where('tipo_movimentacao', Movimentacao::TIPO_RESPONSABILIDADE)->sole();

        $this->devolver($equipamento, 'manutencao', 'tela piscando')->assertSessionHasNoErrors();
        $devolucao = Movimentacao::where('tipo_movimentacao', Movimentacao::TIPO_DEVOLUCAO)->sole();

        $this->assertSame([
            ['evento' => 'cadastro', 'anterior' => null, 'novo' => 'disponivel'],
            ['evento' => 'emprestimo', 'anterior' => 'disponivel', 'novo' => 'em_uso'],
            ['evento' => 'devolucao', 'anterior' => 'em_uso', 'novo' => 'em_manutencao'],
        ], $this->linhaDoTempo($equipamento));

        [, $emprestimo, $retorno] = $equipamento->historicos()->orderBy('id')->get()->all();
        $this->assertSame($termo->id, $emprestimo->movimentacao_id);
        $this->assertSame($devolucao->id, $retorno->movimentacao_id);
        $this->assertSame('Motivo: Manutenção. tela piscando', $retorno->observacao);
        $this->assertSame($this->usuario->id, $retorno->usuario_id);
    }

    public function test_alteracao_manual_de_status_registra_evento_e_edicao_sem_mudar_status_nao(): void
    {
        $equipamento = Equipamento::factory()->create();

        $this->editarStatus($equipamento, 'disponivel')->assertSessionHasNoErrors();
        $this->editarStatus($equipamento, 'defeituoso')->assertSessionHasNoErrors();

        $this->assertSame([
            ['evento' => 'cadastro', 'anterior' => null, 'novo' => 'disponivel'],
            ['evento' => 'alteracao_status', 'anterior' => 'disponivel', 'novo' => 'defeituoso'],
        ], $this->linhaDoTempo($equipamento));
    }

    public function test_transicao_de_status_tem_rotulos_legiveis(): void
    {
        $equipamento = Equipamento::factory()->create();
        $this->editarStatus($equipamento, 'em_manutencao');

        $evento = $equipamento->historicos()->where('evento', 'alteracao_status')->sole();

        $this->assertSame('Alteração manual de status', $evento->evento_rotulo);
        $this->assertSame('Disponível → Em manutenção', $evento->transicao_status);
    }

    // ---------------------------------------------------------------- status após devolução

    public static function motivosDeDevolucao(): array
    {
        return [
            'devolução normal' => ['devolucao', 'disponivel'],
            'manutenção' => ['manutencao', 'em_manutencao'],
            'defeito' => ['defeito', 'defeituoso'],
            'quebra' => ['quebra', 'defeituoso'],
        ];
    }

    #[DataProvider('motivosDeDevolucao')]
    public function test_status_do_equipamento_apos_a_devolucao(string $motivo, string $statusEsperado): void
    {
        $equipamento = Equipamento::factory()->create();
        $this->emprestar([$equipamento]);

        $this->devolver($equipamento, $motivo)->assertSessionHasNoErrors();

        $this->assertSame($statusEsperado, $equipamento->refresh()->status);
    }

    // ---------------------------------------------------------------- "Em uso" só pelas movimentações

    public function test_cadastro_nao_aceita_status_em_uso(): void
    {
        $equipamento = Equipamento::factory()->make();

        $this->post(route('equipamentos.store'), [
            'tipo_equipamento_id' => $equipamento->tipo_equipamento_id,
            'fabricante' => 'Dell',
            'modelo' => 'OptiPlex 3050',
            'status' => 'em_uso',
        ])->assertSessionHasErrors('status');
    }

    public function test_edicao_nao_coloca_em_uso_sem_termo_de_responsabilidade(): void
    {
        $equipamento = Equipamento::factory()->create();

        $this->editarStatus($equipamento, 'em_uso')
            ->assertSessionHasErrors(['status' => 'O status "Em uso" é definido automaticamente ao registrar um termo de responsabilidade.']);

        $this->assertSame('disponivel', $equipamento->refresh()->status);
    }

    public function test_edicao_nao_tira_de_em_uso_com_emprestimo_em_aberto(): void
    {
        $equipamento = Equipamento::factory()->create();
        $this->emprestar([$equipamento]);
        $termo = Movimentacao::sole();

        $this->editarStatus($equipamento, 'disponivel')
            ->assertSessionHasErrors(['status' => "Equipamento em uso pela movimentação #{$termo->id}. Registre a devolução para alterar o status."]);

        $this->assertSame('em_uso', $equipamento->refresh()->status);
    }

    public function test_edicao_de_equipamento_em_uso_mantem_o_status(): void
    {
        $equipamento = Equipamento::factory()->create();
        $this->emprestar([$equipamento]);

        $this->get(route('equipamentos.edit', $equipamento))
            ->assertOk()
            ->assertSee('Para alterar, registre a devolução.');

        $this->editarStatus($equipamento, 'em_uso')->assertSessionHasNoErrors();
        $this->assertSame('em_uso', $equipamento->refresh()->status);
    }

    public static function statusIndisponiveis(): array
    {
        return [
            'em uso' => ['em_uso'],
            'em manutenção' => ['em_manutencao'],
            'defeituoso' => ['defeituoso'],
            'descartado' => ['descartado'],
        ];
    }

    #[DataProvider('statusIndisponiveis')]
    public function test_so_empresta_equipamento_disponivel(string $status): void
    {
        $equipamento = Equipamento::factory()->create(['status' => $status]);

        $this->emprestar([$equipamento])
            ->assertSessionHasErrors(['equipamentos' => 'Um ou mais equipamentos selecionados não estão disponíveis para movimentação.']);

        $this->assertSame(0, Movimentacao::count());
    }

    public function test_equipamento_inativo_nao_pode_ser_emprestado(): void
    {
        $equipamento = Equipamento::factory()->create(['ativo' => false]);

        $this->emprestar([$equipamento])->assertSessionHasErrors('equipamentos');
    }

    public function test_equipamento_nao_pode_ser_emprestado_duas_vezes(): void
    {
        $equipamento = Equipamento::factory()->create();
        $outroFuncionario = Funcionario::factory()->create();

        $this->emprestar([$equipamento])->assertSessionHasNoErrors();
        $this->emprestar([$equipamento], $outroFuncionario)->assertSessionHasErrors('equipamentos');

        $this->assertSame(1, MovimentacaoEquipamento::where('equipamento_id', $equipamento->id)->count());
    }

    public function test_devolucao_so_de_equipamento_em_uso_pelo_funcionario(): void
    {
        $equipamento = Equipamento::factory()->create();
        $outroFuncionario = Funcionario::factory()->create();
        $this->emprestar([$equipamento], $outroFuncionario);

        $this->devolver($equipamento)
            ->assertSessionHasErrors(['equipamentos' => 'Um ou mais equipamentos selecionados não estão mais em responsabilidade em aberto para este funcionário.']);

        $this->assertSame('em_uso', $equipamento->refresh()->status);
    }

    public function test_equipamento_devolvido_pode_ser_emprestado_de_novo(): void
    {
        $equipamento = Equipamento::factory()->create();
        $this->emprestar([$equipamento]);
        $this->devolver($equipamento);

        $this->emprestar([$equipamento])->assertSessionHasNoErrors();

        $this->assertSame('em_uso', $equipamento->refresh()->status);
        $this->assertSame(2, $equipamento->historicos()->where('evento', 'emprestimo')->count());
    }

    // ---------------------------------------------------------------- exclusão

    public function test_nao_exclui_equipamento_com_emprestimo_em_aberto(): void
    {
        $equipamento = Equipamento::factory()->create();
        $this->emprestar([$equipamento]);

        $this->from(route('equipamentos.show', $equipamento))
            ->delete(route('equipamentos.destroy', $equipamento))
            ->assertSessionHas('error');

        $this->assertNotSoftDeleted($equipamento);
    }

    public function test_exclui_equipamento_sem_emprestimo_em_aberto(): void
    {
        $equipamento = Equipamento::factory()->create();
        $this->emprestar([$equipamento]);
        $this->devolver($equipamento);

        $this->delete(route('equipamentos.destroy', $equipamento))->assertSessionHas('success');

        $this->assertSoftDeleted($equipamento);
    }

    // ---------------------------------------------------------------- histórico com funcionário excluído (#2)

    public function test_historico_mantem_o_funcionario_excluido_logicamente(): void
    {
        $equipamento = Equipamento::factory()->create();
        $this->emprestar([$equipamento]);
        $this->devolver($equipamento);
        $nome = $this->funcionario->nome_completo;

        $this->funcionario->delete();

        $registro = MovimentacaoEquipamento::historicoResponsabilidadePorEquipamento($equipamento->id)->sole();
        $this->assertSame($nome, $registro->movimentacao->funcionario->nome_completo);
        $this->assertNotNull($registro->movimentacao->funcionario->apagado_em);

        $this->get(route('relatorios.equipamentos.historico', $equipamento))->assertOk();
    }
}
