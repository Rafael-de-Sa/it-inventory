<?php

namespace Tests\Feature\Layout;

use App\Models\Equipamento;
use App\Models\Funcionario;
use App\Models\Movimentacao;
use App\Models\MovimentacaoEquipamento;
use App\Models\Ocorrencia;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Dashboard da tela inicial por perfil (issue #11), mantendo os cards informativos.
 */
class PainelInicialTest extends TestCase
{
    use RefreshDatabase;

    public function test_tic_ve_parque_ocorrencias_custos_e_atalhos(): void
    {
        $this->autenticar();

        $equipamento = Equipamento::factory()->create(['fabricante' => 'Sunmi', 'modelo' => 'T6900', 'valor_compra' => 1000]);
        Ocorrencia::factory()->create([
            'equipamento_id' => $equipamento->id, 'problema' => 'SecStatus 0x2014',
            'previsao_em' => today()->subDay(), 'reportado_em' => today()->subDays(3), 'custo_manutencao' => 250,
        ]);

        $this->get(route('/'))->assertOk()
            ->assertSee('Boas-vindas ao')              // cards informativos continuam
            ->assertSee('Parque por situação')
            ->assertSee('Disponíveis para entrega, por tipo')
            ->assertSee('1 com previsão vencida')
            ->assertSee('SecStatus 0x2014')
            ->assertSee('R$ 250,00')
            ->assertSee('25%')                           // custo sobre o valor de compra
            ->assertSee('Termo de troca')
            ->assertSee('Cadastrar equipamento')
            ->assertDontSee('Funcionários com pendências');
    }

    public function test_dp_ve_funcionarios_e_pendencias_sem_dados_da_ti(): void
    {
        $desligado = Funcionario::factory()->desligado()->create(['nome' => 'Joana']);
        MovimentacaoEquipamento::factory()->create([
            'movimentacao_id' => Movimentacao::factory()->create(['funcionario_id' => $desligado->id]),
        ]);
        Ocorrencia::factory()->create(['custo_manutencao' => 999]);

        $this->autenticar(Usuario::factory()->departamentoPessoal()->create());

        $this->get(route('/'))->assertOk()
            ->assertSee('Boas-vindas ao')
            ->assertSee('Funcionários com pendências')
            ->assertSee('Joana')
            ->assertSee('Desligado em')
            ->assertSee('Encaminhe à TI para a devolução')
            ->assertSee('Cadastrar funcionário')
            ->assertDontSee('Parque por situação')
            ->assertDontSee('Custo de manutenção')
            ->assertDontSee('R$ 999,00')
            ->assertDontSee('Termo de troca');
    }
}
