<?php

namespace Tests\Feature\Ocorrencias;

use App\Enums\CategoriaEquipamento;
use App\Models\Equipamento;
use App\Models\Funcionario;
use App\Models\Movimentacao;
use App\Models\Ocorrencia;
use App\Models\TipoEquipamento;
use App\Services\IndicadoresManutencao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Ciclo completo da ocorrência, pelas telas, na ordem em que a TIC usaria:
 * cadastro → termo de responsabilidade → ocorrência (recolhe) → termo de devolução → liberação com custo
 * → devolver ao funcionário → indicadores, relatórios e dashboard.
 */
class CicloOcorrenciaTest extends TestCase
{
    use RefreshDatabase;

    public function test_ciclo_completo_da_ocorrencia(): void
    {
        Storage::fake('local');
        $this->autenticar();
        $funcionario = Funcionario::factory()->create(['nome' => 'Maria', 'sobrenome' => 'Souza']);
        $pdf = fn () => UploadedFile::fake()->create('termo.pdf', 50, 'application/pdf');

        // 1. Cadastro do celular (dia 1).
        $this->travelTo('2026-09-01 09:00');
        $tipo = TipoEquipamento::factory()->categoria(CategoriaEquipamento::DISPOSITIVO_MOVEL)->create(['nome' => 'Celular']);
        $this->post(route('equipamentos.store'), [
            'tipo_equipamento_id' => $tipo->id,
            'fabricante' => 'Samsung',
            'modelo' => 'Galaxy A05s',
            'identificacao' => 'CELUR01',
            'valor_compra' => '1.200,00',
            'status' => 'disponivel',
            'dispositivo_movel' => ['imei_1' => '354494165730091'],
        ])->assertSessionHasNoErrors();
        $equipamento = Equipamento::sole();

        // 2. Entrega à Maria, com o termo assinado.
        $this->post(route('movimentacoes.store'), [
            'empresa_id' => $funcionario->setor->empresa_id,
            'setor_id' => $funcionario->setor_id,
            'funcionario_id' => $funcionario->id,
            'equipamentos' => [$equipamento->id],
        ])->assertSessionHasNoErrors();
        $termo = Movimentacao::sole();
        $this->post(route('movimentacoes.upload-termo-responsabilidade', $termo), ['arquivo_termo' => $pdf()])
            ->assertSessionHasNoErrors();
        $this->assertSame('em_uso', $equipamento->fresh()->status);
        $this->assertSame('concluida', $termo->fresh()->status);

        // 3. Problema reportado no dia 5: o formulário mostra a Maria, e a ocorrência recolhe o celular.
        $this->travelTo('2026-09-05 09:00');
        $this->get(route('ocorrencias.create', ['equipamento_id' => $equipamento->id]))->assertOk()
            ->assertSee('"nome":"Maria Souza')->assertSee('Recolher o equipamento para manutenção');

        $this->post(route('ocorrencias.store'), [
            'equipamento_id' => $equipamento->id,
            'reportado_em' => '2026-09-05',
            'data_problema' => '2026-09-04',
            'problema' => 'Tela piscando',
            'canal' => 'GLPI',
            'protocolo' => '12167',
            'previsao_em' => '2026-09-10',
            'recolher' => '1',
        ])->assertSessionHasNoErrors();

        $ocorrencia = Ocorrencia::sole();
        $devolucao = $ocorrencia->devolucao;
        $this->assertSame($funcionario->id, $ocorrencia->funcionario_id);
        $this->assertSame('em_manutencao', $equipamento->fresh()->status);
        $this->assertSame('encerrada', $termo->fresh()->status);
        $this->assertSame('pendente', $devolucao->status);

        // O DP vê a Maria com pendência (termo de devolução a assinar); o dashboard da TIC mostra a ocorrência.
        $this->assertTrue($funcionario->fresh()->obterRestricoesDesligamento()['termos_devolucao_pendentes']);
        $this->get(route('/'))->assertSee('Tela piscando');

        // 4. Termo de devolução gerado e assinado.
        $this->get(route('movimentacoes.termo-devolucao', $devolucao))->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->post(route('movimentacoes.upload-termo-devolucao', $devolucao), ['arquivo_termo' => $pdf()])
            ->assertSessionHasNoErrors();
        $this->assertSame('encerrada', $devolucao->fresh()->status);
        $this->assertFalse($funcionario->fresh()->obterRestricoesDesligamento()['termos_devolucao_pendentes']);

        // 5. Liberado no dia 9, com custo e assistência.
        $this->travelTo('2026-09-09 09:00');
        $this->get(route('ocorrencias.show', $ocorrencia))->assertSee('Encerrar ocorrência');
        $this->post(route('ocorrencias.encerrar', $ocorrencia), [
            'liberado_em' => '2026-09-09',
            'solucao' => 'Troca do display',
            'custo_manutencao' => '350,00',
            'fornecedor' => 'Assistência Central',
        ])->assertSessionHasNoErrors();
        $this->assertSame('disponivel', $equipamento->fresh()->status);

        // 6. Indicadores no dia 10: 4 dias parado em 9 desde o cadastro; custo de 29,2% da compra.
        $this->travelTo('2026-09-10 09:00');
        $indicadores = IndicadoresManutencao::doEquipamento($equipamento->fresh());
        $this->assertSame(350.0, $indicadores['custo']);
        $this->assertSame(29.2, $indicadores['percentual_custo']);
        $this->assertSame(4 * 86400, $indicadores['segundos_parado']);
        $this->assertSame(55.6, $indicadores['disponibilidade']);
        $this->assertSame(4.0, $indicadores['resolucao_media_dias']);

        $this->get(route('equipamentos.show', $equipamento))->assertOk()
            ->assertSee('R$ 350,00 (29,2% do valor de compra)')->assertSee('4 dias')->assertSee('55,6%');
        $this->get(route('relatorios.ocorrencia', $ocorrencia))->assertOk()->assertHeader('Content-Type', 'application/pdf');

        // 7. Devolver à Maria: o termo abre preenchido e, depois de emprestado, a opção some.
        $url = $ocorrencia->fresh()->urlDevolverAoFuncionario();
        $this->get(route('ocorrencias.show', $ocorrencia))->assertSee('Devolver ao funcionário');
        $this->get($url)->assertOk()->assertSee('data-old-funcionario-id="' . $funcionario->id . '"', false);

        $this->post(route('movimentacoes.store'), [
            'empresa_id' => $funcionario->setor->empresa_id,
            'setor_id' => $funcionario->setor_id,
            'funcionario_id' => $funcionario->id,
            'equipamentos' => [$equipamento->id],
        ])->assertSessionHasNoErrors();
        $this->assertSame('em_uso', $equipamento->fresh()->status);
        $this->get(route('ocorrencias.show', $ocorrencia))->assertDontSee('Devolver ao funcionário');

        // 8. Linha do tempo completa e relatórios.
        $this->assertSame(
            [
                ['cadastro', null, 'disponivel'],
                ['emprestimo', 'disponivel', 'em_uso'],
                ['devolucao', 'em_uso', 'em_manutencao'],
                ['ocorrencia', 'em_manutencao', 'disponivel'],
                ['emprestimo', 'disponivel', 'em_uso'],
            ],
            $equipamento->historicos()->orderBy('id')->get()
                ->map(fn ($h) => [$h->evento, $h->status_anterior, $h->status_novo])->all(),
        );

        $this->get(route('relatorios.equipamentos.historico', $equipamento))->assertOk();
        $this->get(route('/'))->assertSee('R$ 350,00')->assertSee('Galaxy A05s');
    }
}
