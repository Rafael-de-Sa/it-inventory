<?php

namespace Tests\Feature\Ocorrencias;

use App\Models\Equipamento;
use App\Models\Funcionario;
use App\Models\Movimentacao;
use App\Models\MovimentacaoEquipamento;
use App\Models\Ocorrencia;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Ocorrências de equipamento (issue #10): registro, liberação e efeito no status do equipamento.
 */
class OcorrenciaTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $usuario;

    protected function setUp(): void
    {
        parent::setUp();

        $this->usuario = $this->autenticar();
    }

    private function registrar(Equipamento $equipamento, array $dados = [])
    {
        return $this->post(route('ocorrencias.store'), array_merge([
            'equipamento_id' => $equipamento->id,
            'reportado_em' => today()->format('Y-m-d'),
            'problema' => '  Erro   SecStatus 0x2014 ',
            'canal' => 'GLPI',
            'protocolo' => '12167',
        ], $dados));
    }

    private function atualizar(Ocorrencia $ocorrencia, array $dados)
    {
        return $this->put(route('ocorrencias.update', $ocorrencia), array_merge([
            'reportado_em' => $ocorrencia->reportado_em->format('Y-m-d'),
            'problema' => $ocorrencia->problema,
        ], $dados));
    }

    /** Equipamento emprestado a um funcionário (termo de responsabilidade em aberto). */
    private function emUsoCom(Funcionario $funcionario): Equipamento
    {
        $item = MovimentacaoEquipamento::factory()->create([
            'movimentacao_id' => Movimentacao::factory()->create(['funcionario_id' => $funcionario->id]),
        ]);

        return $item->equipamento;
    }

    public function test_equipamento_com_a_ti_vai_para_manutencao_e_volta_ao_ser_liberado(): void
    {
        $equipamento = Equipamento::factory()->create();

        $this->registrar($equipamento)->assertSessionHasNoErrors();

        $ocorrencia = Ocorrencia::sole();
        $this->assertSame('Erro SecStatus 0x2014', $ocorrencia->problema);
        $this->assertSame($this->usuario->id, $ocorrencia->usuario_id);
        $this->assertTrue($ocorrencia->alterou_status);
        $this->assertSame('em_manutencao', $equipamento->fresh()->status);

        $evento = $equipamento->historicos()->latest('id')->first();
        $this->assertSame('ocorrencia', $evento->evento);
        $this->assertStringContainsString("Ocorrência #{$ocorrencia->id}", $evento->observacao);

        $this->atualizar($ocorrencia, ['liberado_em' => today()->format('Y-m-d'), 'solucao' => 'Limpeza do app'])
            ->assertSessionHasNoErrors();

        $this->assertFalse($ocorrencia->fresh()->estaAberta());
        $this->assertSame('disponivel', $equipamento->fresh()->status);
    }

    public function test_equipamento_em_uso_continua_com_o_funcionario(): void
    {
        $funcionario = Funcionario::factory()->create();
        $equipamento = $this->emUsoCom($funcionario);

        $this->registrar($equipamento)->assertSessionHasNoErrors();

        $ocorrencia = Ocorrencia::sole();
        $this->assertSame('em_uso', $equipamento->fresh()->status);
        $this->assertFalse($ocorrencia->alterou_status);
        // Sem último usuário informado, vale quem está com o equipamento.
        $this->assertSame($funcionario->id, $ocorrencia->funcionario_id);

        $this->get(route('ocorrencias.show', $ocorrencia))->assertOk()->assertSee('Registrar troca');
    }

    public function test_recolher_registra_a_devolucao_e_gera_o_termo(): void
    {
        $funcionario = Funcionario::factory()->create();
        $equipamento = $this->emUsoCom($funcionario);
        $termoOriginal = MovimentacaoEquipamento::where('equipamento_id', $equipamento->id)->value('movimentacao_id');

        $this->get(route('ocorrencias.create', ['equipamento_id' => $equipamento->id]))->assertOk()
            ->assertSee('Recolher o equipamento para manutenção')
            ->assertSee('"nome":"' . $funcionario->nome_completo);

        $this->registrar($equipamento, ['recolher' => '1'])->assertSessionHasNoErrors()
            ->assertSessionHas('success', fn ($mensagem) => str_contains($mensagem, 'termo de devolução'));

        $ocorrencia = Ocorrencia::sole();
        $devolucao = $ocorrencia->devolucao;

        $this->assertSame(Movimentacao::TIPO_DEVOLUCAO, $devolucao->tipo_movimentacao);
        $this->assertSame('pendente', $devolucao->status);
        $this->assertSame($funcionario->id, $devolucao->funcionario_id);
        $this->assertSame($funcionario->id, $ocorrencia->funcionario_id);
        $this->assertTrue($ocorrencia->alterou_status);

        $item = MovimentacaoEquipamento::where('movimentacao_id', $termoOriginal)->sole();
        $this->assertSame('manutencao', $item->motivo_devolucao);
        $this->assertSame($devolucao->id, $item->devolucao_movimentacao_id);

        $this->assertSame('em_manutencao', $equipamento->fresh()->status);
        $this->assertNull($equipamento->fresh()->emprestimoEmAberto());
        $this->assertSame('encerrada', Movimentacao::find($termoOriginal)->status);
        // O termo de devolução fica pendente de assinatura (bloqueia o desligamento até ser enviado).
        $this->assertTrue($funcionario->fresh()->obterRestricoesDesligamento()['termos_devolucao_pendentes']);

        $this->get(route('ocorrencias.show', $ocorrencia))->assertOk()
            ->assertSee('Equipamento recolhido para manutenção')->assertSee('Gerar termo de devolução')
            ->assertDontSee('Registrar troca');
        $this->get(route('movimentacoes.termo-devolucao', $devolucao))->assertOk();

        // Liberado: volta para a TI como disponível.
        $this->atualizar($ocorrencia, ['liberado_em' => today()->format('Y-m-d'), 'solucao' => 'Tela substituída'])
            ->assertSessionHasNoErrors();
        $this->assertSame('disponivel', $equipamento->fresh()->status);
    }

    public function test_devolver_ao_funcionario_so_ate_o_equipamento_ser_emprestado_de_novo(): void
    {
        $funcionario = Funcionario::factory()->create();
        $equipamento = $this->emUsoCom($funcionario);

        $this->registrar($equipamento, ['recolher' => '1'])->assertSessionHasNoErrors();
        $ocorrencia = Ocorrencia::sole();

        // Aberta: ainda em manutenção, sem opção de devolver.
        $this->get(route('ocorrencias.show', $ocorrencia))->assertDontSee('Devolver ao funcionário');

        $this->atualizar($ocorrencia, ['liberado_em' => today()->format('Y-m-d'), 'solucao' => 'Tela substituída'])
            ->assertSessionHasNoErrors();

        $url = $ocorrencia->fresh()->urlDevolverAoFuncionario();
        $this->assertNotNull($url);
        $this->get(route('ocorrencias.show', $ocorrencia))->assertSee('Devolver ao funcionário');

        // O termo de responsabilidade abre com o funcionário e o equipamento selecionados.
        $this->get($url)->assertOk()
            ->assertSee('data-old-funcionario-id="' . $funcionario->id . '"', false)
            ->assertSee('data-old-equipamentos="[' . $equipamento->id . ']"', false);

        // Emprestado de novo (aqui para outra pessoa): a opção some, e continua sem aparecer depois da devolução.
        $outro = Funcionario::factory()->create();
        $this->post(route('movimentacoes.store'), [
            'empresa_id' => $outro->setor->empresa_id,
            'setor_id' => $outro->setor_id,
            'funcionario_id' => $outro->id,
            'equipamentos' => [$equipamento->id],
        ])->assertSessionHasNoErrors();
        $this->assertNull($ocorrencia->fresh()->urlDevolverAoFuncionario());

        $this->post(route('movimentacoes.devolucao.store'), [
            'empresa_id' => $outro->setor->empresa_id,
            'setor_id' => $outro->setor_id,
            'funcionario_id' => $outro->id,
            'equipamentos' => [$equipamento->id],
            'motivos_devolucao_equipamentos' => [$equipamento->id => 'devolucao'],
        ])->assertSessionHasNoErrors();
        $this->assertSame('disponivel', $equipamento->fresh()->status);
        $this->get(route('ocorrencias.show', $ocorrencia))->assertDontSee('Devolver ao funcionário');
    }

    public function test_recolher_equipamento_com_a_ti_so_coloca_em_manutencao(): void
    {
        $equipamento = Equipamento::factory()->create();

        $this->registrar($equipamento, ['recolher' => '1'])->assertSessionHasNoErrors();

        $this->assertNull(Ocorrencia::sole()->devolucao_movimentacao_id);
        $this->assertSame('em_manutencao', $equipamento->fresh()->status);
        $this->assertSame(0, Movimentacao::where('tipo_movimentacao', Movimentacao::TIPO_DEVOLUCAO)->count());
    }

    public function test_ultimo_usuario_informado_prevalece(): void
    {
        $equipamento = $this->emUsoCom(Funcionario::factory()->create());
        $outro = Funcionario::factory()->create();

        $this->registrar($equipamento, ['funcionario_id' => $outro->id])->assertSessionHasNoErrors();

        $this->assertSame($outro->id, Ocorrencia::sole()->funcionario_id);
    }

    public function test_ocorrencia_registrada_ja_resolvida_nao_muda_o_status(): void
    {
        $equipamento = Equipamento::factory()->create();

        $this->registrar($equipamento, ['liberado_em' => today()->format('Y-m-d'), 'solucao' => 'Reinstalado'])
            ->assertSessionHasNoErrors();

        $this->assertSame('disponivel', $equipamento->fresh()->status);
    }

    public function test_liberacao_nao_mexe_em_status_alterado_por_outra_via(): void
    {
        $equipamento = Equipamento::factory()->create(['status' => 'defeituoso']);
        $this->registrar($equipamento)->assertSessionHasNoErrors();

        $this->atualizar(Ocorrencia::sole(), ['liberado_em' => today()->format('Y-m-d'), 'solucao' => 'Sem conserto'])
            ->assertSessionHasNoErrors();

        $this->assertSame('defeituoso', $equipamento->fresh()->status);
    }

    public function test_validacoes_de_datas_e_solucao(): void
    {
        $equipamento = Equipamento::factory()->create();

        $this->registrar($equipamento, ['data_problema' => today()->addDay()->format('Y-m-d')])
            ->assertSessionHasErrors('data_problema');
        $this->registrar($equipamento, ['reportado_em' => today()->addDay()->format('Y-m-d')])
            ->assertSessionHasErrors('reportado_em');
        $this->registrar($equipamento, ['previsao_em' => today()->subDay()->format('Y-m-d')])
            ->assertSessionHasErrors('previsao_em');
        $this->registrar($equipamento, ['liberado_em' => today()->format('Y-m-d')])
            ->assertSessionHasErrors(['solucao' => 'Informe a solução ao liberar o equipamento.']);
        $this->registrar($equipamento, ['problema' => ''])->assertSessionHasErrors('problema');

        $this->assertSame(0, Ocorrencia::count());
    }

    public function test_equipamento_baixado_nao_recebe_ocorrencia(): void
    {
        $this->registrar(Equipamento::factory()->create(['status' => 'baixado']))
            ->assertSessionHasErrors('equipamento_id');
    }

    public function test_valor_cobrado_no_formato_brasileiro(): void
    {
        $this->registrar(Equipamento::factory()->create(), ['valor_cobrado' => '1.234,56'])->assertSessionHasNoErrors();

        $this->assertSame('1234.56', Ocorrencia::sole()->valor_cobrado);
        $this->assertSame('R$ 1.234,56', Ocorrencia::sole()->valor_cobrado_formatado);
    }

    public function test_excluir_ocorrencia_aberta_desfaz_a_manutencao(): void
    {
        $equipamento = Equipamento::factory()->create();
        $this->registrar($equipamento)->assertSessionHasNoErrors();

        $ocorrencia = Ocorrencia::sole();
        $this->delete(route('ocorrencias.destroy', $ocorrencia))->assertRedirect(route('ocorrencias.index'));

        $this->assertSoftDeleted($ocorrencia);
        $this->assertSame('disponivel', $equipamento->fresh()->status);
    }

    public function test_listagem_filtra_por_situacao_e_busca(): void
    {
        $aberta = Ocorrencia::factory()->create(['problema' => 'Bloqueio poltrona']);
        $resolvida = Ocorrencia::factory()->resolvida()->create(['problema' => 'BPE veio nulo']);

        $this->get(route('ocorrencias.index'))->assertOk()
            ->assertSee('Bloqueio poltrona')->assertDontSee('BPE veio nulo');

        $this->get(route('ocorrencias.index', ['situacao' => 'resolvidas']))
            ->assertSee('BPE veio nulo')->assertDontSee('Bloqueio poltrona');

        $this->get(route('ocorrencias.index', ['situacao' => 'todas', 'busca' => 'poltrona']))
            ->assertSee('Bloqueio poltrona')->assertDontSee('BPE veio nulo');

        $this->get(route('ocorrencias.create', ['equipamento_id' => $aberta->equipamento_id]))->assertOk();
        $this->get(route('ocorrencias.edit', $resolvida))->assertOk();
    }

    public function test_relatorios_mostram_as_ocorrencias(): void
    {
        $funcionario = Funcionario::factory()->create();
        $equipamento = $this->emUsoCom($funcionario);
        $ocorrencia = Ocorrencia::factory()->create([
            'equipamento_id' => $equipamento->id,
            'funcionario_id' => $funcionario->id,
            'problema' => 'Tela quebrada',
            'valor_cobrado' => 150,
        ]);

        $this->get(route('relatorios.equipamentos.historico', $equipamento))->assertOk();
        $this->get(route('relatorios.funcionarios.equipamentos', $funcionario))->assertOk();

        // O valor cobrado do colaborador não vai para o relatório do funcionário (gerado pelo DP).
        $html = view('relatorios.funcionarios.equipamentos-por-funcionario', [
            'funcionario' => $funcionario->load('setor.empresa'),
            'listaDeEquipamentosEmUso' => collect(),
            'dataGeracaoRelatorio' => now(),
        ])->render();
        $this->assertStringNotContainsString('150,00', $html);


        $this->get(route('equipamentos.show', $equipamento))->assertOk()->assertSee('Tela quebrada')->assertSee('Trocar');
    }

    public function test_perfil_dp_nao_acessa_ocorrencias(): void
    {
        $this->autenticar(Usuario::factory()->departamentoPessoal()->create());

        $this->get(route('ocorrencias.index'))->assertForbidden();
        $this->registrar(Equipamento::factory()->create())->assertForbidden();
    }
}
