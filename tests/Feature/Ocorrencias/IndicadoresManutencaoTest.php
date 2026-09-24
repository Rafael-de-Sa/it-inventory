<?php

namespace Tests\Feature\Ocorrencias;

use App\Models\Equipamento;
use App\Models\Ocorrencia;
use App\Models\Usuario;
use App\Services\IndicadoresManutencao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Indicadores de manutenção (issue #11): custo, tempo parado, disponibilidade e tempo médio de resolução,
 * e o relatório da ocorrência com o acumulado do equipamento.
 */
class IndicadoresManutencaoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->autenticar();
    }

    /** Muda o status em uma data, registrando o evento na linha do tempo como a tela de edição faria. */
    private function mudarStatus(Equipamento $equipamento, string $status, string $data): void
    {
        $this->travelTo($data);
        $equipamento->refresh()->update(['status' => $status]);
    }

    public function test_tempo_parado_e_disponibilidade_pela_linha_do_tempo(): void
    {
        $this->travelTo('2026-01-01 08:00');
        $equipamento = Equipamento::factory()->create();

        $this->mudarStatus($equipamento, 'em_manutencao', '2026-01-11 08:00');
        $this->mudarStatus($equipamento, 'disponivel', '2026-01-16 08:00');
        $this->travelTo('2026-01-21 08:00');

        $indicadores = IndicadoresManutencao::doEquipamento($equipamento->refresh());

        // 5 dias parado em 20 dias desde o cadastro.
        $this->assertSame(5 * 86400, $indicadores['segundos_parado']);
        $this->assertSame(75.0, $indicadores['disponibilidade']);
        $this->assertSame('5 dias', IndicadoresManutencao::duracao($indicadores['segundos_parado']));
        $this->assertSame('75%', IndicadoresManutencao::percentual($indicadores['disponibilidade']));
    }

    public function test_defeituoso_ate_hoje_conta_como_parado_e_a_baixa_encerra_a_contagem(): void
    {
        $this->travelTo('2026-01-01 08:00');
        $equipamento = Equipamento::factory()->create();

        $this->mudarStatus($equipamento, 'defeituoso', '2026-01-06 08:00');
        $this->travelTo('2026-01-11 08:00');
        $this->assertSame(50.0, IndicadoresManutencao::doEquipamento($equipamento->refresh())['disponibilidade']);

        // Baixado no dia 11: o tempo depois disso não conta, mesmo consultando um mês depois.
        $this->mudarStatus($equipamento, 'baixado', '2026-01-11 08:00');
        $this->travelTo('2026-02-11 08:00');
        $indicadores = IndicadoresManutencao::doEquipamento($equipamento->refresh());

        $this->assertSame(5 * 86400, $indicadores['segundos_parado']);
        $this->assertSame(50.0, $indicadores['disponibilidade']);
    }

    public function test_custo_percentual_da_compra_e_resolucao_media(): void
    {
        $equipamento = Equipamento::factory()->create(['valor_compra' => 1000]);

        Ocorrencia::factory()->create([
            'equipamento_id' => $equipamento->id, 'custo_manutencao' => 150,
            'reportado_em' => '2026-03-10', 'liberado_em' => '2026-03-14', 'solucao' => 'Troca da tela',
        ]);
        Ocorrencia::factory()->create([
            'equipamento_id' => $equipamento->id, 'custo_manutencao' => 50,
            'reportado_em' => '2026-04-01', 'liberado_em' => '2026-04-03', 'solucao' => 'Bateria',
        ]);
        Ocorrencia::factory()->create(['equipamento_id' => $equipamento->id]); // aberta, sem custo

        $indicadores = IndicadoresManutencao::doEquipamento($equipamento);

        $this->assertSame(200.0, $indicadores['custo']);
        $this->assertSame(20.0, $indicadores['percentual_custo']);
        $this->assertSame(3, $indicadores['ocorrencias']);
        $this->assertSame(1, $indicadores['abertas']);
        $this->assertSame(3.0, $indicadores['resolucao_media_dias']);

        $this->get(route('equipamentos.show', $equipamento))->assertOk()
            ->assertSee('Indicadores de manutenção')->assertSee('R$ 200,00 (20% do valor de compra)');
    }

    public function test_custo_e_fornecedor_no_registro_da_ocorrencia(): void
    {
        $this->post(route('ocorrencias.store'), [
            'equipamento_id' => Equipamento::factory()->create()->id,
            'reportado_em' => today()->format('Y-m-d'),
            'problema' => 'Tela quebrada',
            'custo_manutencao' => '1.350,00',
            'fornecedor' => 'Assistência Central',
        ])->assertSessionHasNoErrors();

        $ocorrencia = Ocorrencia::sole();
        $this->assertSame('1350.00', $ocorrencia->custo_manutencao);
        $this->assertSame('Assistência Central', $ocorrencia->fornecedor);

        $this->get(route('ocorrencias.show', $ocorrencia))->assertOk()->assertSee('R$ 1.350,00');
    }

    public function test_relatorio_da_ocorrencia_com_o_acumulado_do_equipamento(): void
    {
        $equipamento = Equipamento::factory()->create(['valor_compra' => 2000]);
        $ocorrencia = Ocorrencia::factory()->resolvida()->create(['equipamento_id' => $equipamento->id, 'custo_manutencao' => 300]);
        Ocorrencia::factory()->create(['equipamento_id' => $equipamento->id, 'custo_manutencao' => 100]);

        $this->get(route('relatorios.ocorrencia', $ocorrencia))
            ->assertOk()->assertHeader('Content-Type', 'application/pdf');

        $html = view('relatorios.ocorrencias.ocorrencia', [
            'ocorrencia' => $ocorrencia,
            'equipamento' => $equipamento->load('tipoEquipamento'),
            'indicadores' => IndicadoresManutencao::doEquipamento($equipamento),
            'dataHoraEmissao' => now(),
        ])->render();

        $this->assertStringContainsString('RELATÓRIO DE OCORRÊNCIA Nº ' . $ocorrencia->id, $html);
        $this->assertStringContainsString('R$ 300,00', $html); // custo desta ocorrência
        $this->assertStringContainsString('R$ 400,00', $html); // acumulado do equipamento
        $this->assertStringContainsString('20%', $html);

        $this->get(route('relatorios.equipamentos.historico', $equipamento))->assertOk();
    }

    public function test_perfil_dp_nao_gera_relatorio_de_ocorrencia(): void
    {
        $ocorrencia = Ocorrencia::factory()->create();
        $this->autenticar(Usuario::factory()->departamentoPessoal()->create());

        $this->get(route('relatorios.ocorrencia', $ocorrencia))->assertForbidden();
    }
}
