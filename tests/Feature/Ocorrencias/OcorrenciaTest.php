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

        $html = view('relatorios.funcionarios.equipamentos-por-funcionario', [
            'funcionario' => $funcionario->load('setor.empresa'),
            'listaDeEquipamentosEmUso' => collect(),
            'ocorrenciasComValor' => collect([$ocorrencia->load('equipamento.tipoEquipamento')]),
            'dataGeracaoRelatorio' => now(),
        ])->render();

        $this->assertStringContainsString('Valores cobrados em ocorrências', $html);
        $this->assertStringContainsString('150,00', $html);

        $this->get(route('equipamentos.show', $equipamento))->assertOk()->assertSee('Tela quebrada')->assertSee('Trocar');
    }

    public function test_perfil_dp_nao_acessa_ocorrencias(): void
    {
        $this->autenticar(Usuario::factory()->departamentoPessoal()->create());

        $this->get(route('ocorrencias.index'))->assertForbidden();
        $this->registrar(Equipamento::factory()->create())->assertForbidden();
    }
}
